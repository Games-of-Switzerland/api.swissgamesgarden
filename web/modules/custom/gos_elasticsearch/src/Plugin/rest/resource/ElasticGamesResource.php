<?php

namespace Drupal\gos_elasticsearch\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexManager;
use Drupal\gos_elasticsearch\Plugin\rest\ResourceValidator\ElasticGamesResourceValidator;
use Drupal\gos_rest\Plugin\rest\ValidatorFactory;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a Proxy to access to Elasticsearch Games Documents.
 *
 * @RestResource(
 *     id="elasticsearch_games_resource",
 *     label=@Translation("Proxy to access to Elasticsearch Games Documents"),
 *     uri_paths={
 *         "canonical": "/search/games"
 *     }
 * )
 */
class ElasticGamesResource extends ElasticResourceBase {

  /**
   * The Elasticsearch Plugin ID to be used.
   *
   * @var string
   *
   * @see \Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex\MultilingualSiteWideIndex::id
   */
  public const ELASTICSEARCH_PLUGIN_ID = 'gos_index_node_game';

  /**
   * The page size.
   *
   * Use to limit the number of properties returned and optimize response size.
   *
   * @var string
   */
  public const PAGER_SIZE = 25;

  /**
   * The taxonomy term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected $termStorage;

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress MissingParamType
   * @psalm-suppress ArgumentTypeCoercion
   * @psalm-suppress PropertyTypeCoercion
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerChannelInterface $logger,
    ValidatorFactory $validator_factory,
    ElasticsearchIndexManager $elasticsearch_plugin_manager,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger, $validator_factory, $elasticsearch_plugin_manager);
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress PossiblyNullArgument
   * @psalm-suppress ArgumentTypeCoercion
   * @psalm-suppress PossiblyNullReference
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('gos_rest.validator_factory'),
      $container->get('plugin.manager.elasticsearch_index.processor'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Proxy to fetch content on Elasticsearch.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The incoming request.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *
   * @return \Drupal\Core\Cache\CacheableJsonResponse
   *   The Json response.
   */
  public function get(Request $request): CacheableJsonResponse {
    // Setup the base response & cacheable-metadata.
    parent::get($request);

    $resource_validator = $this->buildResourceValidator($request);

    // None valid resource parameters will answer immediately with errors msg.
    if (!$this->isValid($resource_validator)) {
      return $this->buildValidatorErrorResponse($resource_validator);
    }

    /** @var \Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex\GameNodeIndex $index */
    $index = $this->elasticsearchPluginManager->createInstance(self::ELASTICSEARCH_PLUGIN_ID);

    $es_query = [
      'index' => $index->getIndexName([]),
      'from' => $resource_validator->getPage() * self::PAGER_SIZE,
      'size' => self::PAGER_SIZE,
      'body' => [
        'query' => [
          'bool' => [
            'filter' => [
              'bool' => [
                'must' => [],
              ],
            ],
          ],
        ],

        'aggregations' => [
          'aggs_all' => [
            'global' => new stdClass(),
            'aggs' => [
              // Genres aggregations.
              'all_filtered_genres' => [
                'filter' => [
                  'bool' => [
                    // Where all the filter w/o a Score impact should be.
                    'must' => [],
                  ],
                ],
                'aggregations' => [
                  'all_nested_genres' => [
                    'nested' => [
                      'path' => 'genres',
                    ],
                    'aggs' => [
                      'genres_name_keyword' => [
                        'terms' => [
                          'field' => 'genres.slug',
                          'min_doc_count' => 0,
                          'size' => 100,
                        ],
                      ],
                    ],
                  ],
                ],
              ],
              // Platforms aggregations.
              'all_filtered_platforms' => [
                'filter' => [
                  'bool' => [
                    // Where all the filter w/o a Score impact should be.
                    'must' => [],
                  ],
                ],
                'aggregations' => [
                  'all_nested_platforms' => [
                    'nested' => [
                      'path' => 'releases',
                    ],
                    'aggs' => [
                      'platforms_name_keyword' => [
                        'terms' => [
                          'field' => 'releases.platform_slug',
                          'min_doc_count' => 0,
                          'size' => 100,
                        ],
                      ],
                    ],
                  ],
                ],
              ],
              // Stores aggregations.
              'all_filtered_stores' => [
                'filter' => [
                  'bool' => [
                    // Where all the filter w/o a Score impact should be.
                    'must' => [],
                  ],
                ],
                'aggregations' => [
                  'all_nested_stores' => [
                    'nested' => [
                      'path' => 'stores',
                    ],
                    'aggs' => [
                      'stores_name_keyword' => [
                        'terms' => [
                          'field' => 'stores.slug',
                          'min_doc_count' => 0,
                          'size' => 100,
                        ],
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],

        'sort' => [
          '_score' => [
            'order' => 'desc',
          ],
        ],
      ],
    ];

    // Add the sort property.
    if (!empty($resource_validator->getSort())) {
      $es_query['body']['sort'] = $this->addSort($resource_validator->getSort());
    }

    if ($resource_validator->getPlatforms()) {
      // Filter the "hits" by given platform(s).
      $es_query['body']['query']['bool']['filter']['bool']['must'][] = $this->addPlatformsFilter($resource_validator->getPlatforms());

      // Filter the "aggregations" by given platform(s).
      $es_query['body']['aggregations']['aggs_all']['aggs']['all_filtered_genres']['filter']['bool']['should'][] = $this->addPlatformsFilter($resource_validator->getPlatforms());
      $es_query['body']['aggregations']['aggs_all']['aggs']['all_filtered_stores']['filter']['bool']['should'][] = $this->addPlatformsFilter($resource_validator->getPlatforms());
    }

    if ($resource_validator->getGenres()) {
      // Filter the "hits" by given genre(s).
      $es_query['body']['query']['bool']['filter']['bool']['must'][] = $this->addGenresFilter($resource_validator->getGenres());

      // Filter the "aggregations" by given genre(s).
      $es_query['body']['aggregations']['aggs_all']['aggs']['all_filtered_platforms']['filter']['bool']['should'][] = $this->addGenresFilter($resource_validator->getGenres());
      $es_query['body']['aggregations']['aggs_all']['aggs']['all_filtered_stores']['filter']['bool']['should'][] = $this->addGenresFilter($resource_validator->getGenres());
    }

    if ($resource_validator->getStores()) {
      // Filter the "hits" by given store(s).
      $es_query['body']['query']['bool']['filter']['bool']['must'][] = $this->addStoresFilter($resource_validator->getStores());

      // Filter the "aggregations" by given store(s).
      $es_query['body']['aggregations']['aggs_all']['aggs']['all_filtered_platforms']['filter']['bool']['should'][] = $this->addStoresFilter($resource_validator->getStores());
      $es_query['body']['aggregations']['aggs_all']['aggs']['all_filtered_genres']['filter']['bool']['should'][] = $this->addStoresFilter($resource_validator->getStores());
    }

    try {
      $results = $index->search($es_query);
      $this->response->setData($results);
    }
    catch (\Exception $exception) {
      return $this->buildElasticsearchErrorResponse($exception);
    }

    $this->responseCache->addCacheTags([
      'node_list:game',
      'node_list:studio',
      'node_list:people',
    ]);
    $this->response->addCacheableDependency($this->responseCache);

    return $this->response;
  }

  /**
   * Build a sort parameters for query.
   *
   * @param array $sort
   *   The sort property with direction as key.
   *
   * @return array
   *   The sort elasticsearch structure.
   */
  protected function addSort(array $sort): array {
    $direction = key($sort);
    $property = $sort[$direction];

    $order = [];

    switch ($property) {
      case 'releases.date':
        $order = [
          $property => [
            [
              'order' => key($sort),
              'nested_path' => 'releases',
              'missing' => '_last',
            ],
          ],
        ];

        break;

      default:
        $order = [
          $property => [
            ['order' => key($sort)],
          ],
        ];

        break;
    }

    return $order;
  }

  /**
   * Build resource validator from request to validate request parameters.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Request object.
   *
   * @return \Drupal\gos_elasticsearch\Plugin\rest\ResourceValidator\ElasticGamesResourceValidator
   *   Resource validator.
   */
  protected function buildResourceValidator(Request $request): ElasticGamesResourceValidator {
    $resource_validator = new ElasticGamesResourceValidator($request->query->all());
    $resource_validator->setRaw($request->query->all());

    // The platform(s) optional parameter.
    if ($request->query->has('platforms')) {

      /** @var \Drupal\taxonomy\TermInterface[] $platforms */
      $platforms = [];

      foreach ($request->query->get('platforms') as $slug) {
        $platform = $this->termStorage->loadByProperties([
          'vid' => 'platform',
          'field_slug' => $slug,
        ]);

        if (!$platform) {
          continue;
        }

        /** @var \Drupal\taxonomy\TermInterface $platform */
        $platform = reset($platform);
        $platforms[] = $platform;
      }

      $resource_validator->setPlatforms($platforms);
    }

    // The genre(s) optional parameter.
    if ($request->query->has('genres')) {
      $resource_validator->setGenres($request->query->get('genres'));

      /** @var \Drupal\taxonomy\TermInterface[] $genres */
      $genres = [];

      foreach ($request->query->get('genres') as $slug) {
        $genre = $this->termStorage->loadByProperties([
          'vid' => 'genre',
          'field_slug' => $slug,
        ]);

        if (!$genre) {
          continue;
        }

        /** @var \Drupal\taxonomy\TermInterface $genre */
        $genre = reset($genre);
        $genres[] = $genre;
      }

      $resource_validator->setGenres($genres);
    }

    return $resource_validator;
  }

  /**
   * Add a condition to filter games by genres slug.
   *
   * @param \Drupal\taxonomy\TermInterface[] $genres
   *   The collection of genres to use for filtering.
   *
   * @return array
   *   The Nested OR-Condition query to filter-out games by genre.
   */
  private function addGenresFilter(array $genres): array {
    $structure = [
      'nested' => [
        'path' => 'genres',
        'query' => [
          'bool' => [
            'should' => [],
          ],
        ],
      ],
    ];

    foreach ($genres as $genre) {
      $structure['nested']['query']['bool']['should'][] = ['term' => ['genres.slug' => $genre->field_slug->value]];
    }

    return $structure;
  }

  /**
   * Add a condition to filter games by platforms slug.
   *
   * @param \Drupal\taxonomy\TermInterface[] $platforms
   *   The collection of platforms to use for filtering.
   *
   * @return array
   *   The Nested OR-Condition query to filter-out games by release platform.
   */
  private function addPlatformsFilter(array $platforms): array {
    $structure = [
      'nested' => [
        'path' => 'releases',
        'query' => [
          'bool' => [
            'should' => [],
          ],
        ],
      ],
    ];

    foreach ($platforms as $platform) {
      $structure['nested']['query']['bool']['should'][] = ['term' => ['releases.platform_slug' => $platform->field_slug->value]];
    }

    return $structure;
  }

  /**
   * Add a condition to filter games by stores slug.
   *
   * @param array $stores
   *   The collection of stores slug to use for filtering.
   *
   * @return array
   *   The Nested OR-Condition query to filter-out games by stores name.
   */
  private function addStoresFilter(array $stores): array {
    $structure = [
      'nested' => [
        'path' => 'stores',
        'query' => [
          'bool' => [
            'should' => [],
          ],
        ],
      ],
    ];

    foreach ($stores as $store) {
      $structure['nested']['query']['bool']['should'][] = ['term' => ['stores.slug' => $store]];
    }

    return $structure;
  }

}
