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
              'all_genres' => [
                'nested' => [
                  'path' => 'genres',
                ],
                'aggregations' => [
                  'agg_genres' => [
                    'filter' => [
                      'bool' => [
                        // Where all the filter w/o a Score impact should be.
                        'must' => [],
                      ],
                    ],
                    'aggregations' => [
                      'genres_name_keyword' => [
                        'terms' => [
                          'field' => 'genres.name_keyword',
                          'min_doc_count' => 0,
                          'size' => 100,
                        ],
                      ],
                    ],
                  ],
                ],
              ],
              'all_platforms' => [
                'nested' => [
                  'path' => 'releases',
                ],
                'aggregations' => [
                  'agg_platforms' => [
                    'filter' => [
                      'bool' => [
                        // Where all the filter w/o a Score impact should be.
                        'must' => [],
                      ],
                    ],
                    'aggregations' => [
                      'releases_platform_keyword' => [
                        'terms' => [
                          'field' => 'releases.platform_keyword',
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

    if (!empty($resource_validator->getSort())) {
      $es_query['body']['sort'] = $this->addSort($resource_validator->getSort());
    }

    if ($resource_validator->getPlatformsUuid()) {
      $es_query['body']['query']['bool']['filter']['bool']['must'][] = $this->addPlatformsFilter($resource_validator->getPlatformsUuid());

      $es_query['body']['aggregations']['aggs_all']['aggs']['all_genres']['aggregations']['agg_genres']['filter']['bool']['must'][] = $this->addPlatformsFilter($resource_validator->getPlatformsUuid());
    }

    if ($resource_validator->getGenresUuid()) {
      $es_query['body']['query']['bool']['filter']['bool']['must'][] = $this->addGenresFilter($resource_validator->getGenresUuid());

      $es_query['body']['aggregations']['aggs_all']['aggs']['all_platforms']['aggregations']['agg_platforms']['filter']['bool']['must'][] = $this->addGenresFilter($resource_validator->getGenresUuid());
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

    // The platform(s) optional parameter.
    if ($request->query->has('platformsUuid')) {
      $resource_validator->setPlatformsUuid($request->query->get('platformsUuid'));

      /** @var \Drupal\taxonomy\TermInterface[] $platforms */
      $platforms = [];

      $platforms_uuid = $resource_validator->getPlatformsUuid();

      if ($platforms_uuid) {
        foreach ($platforms_uuid as $platform_uuid) {
          $platform = $this->termStorage->loadByProperties([
            'vid' => 'platform',
            'uuid' => $platform_uuid,
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
    }

    // The genre(s) optional parameter.
    if ($request->query->has('genresUuid')) {
      $resource_validator->setGenresUuid($request->query->get('genresUuid'));

      /** @var \Drupal\taxonomy\TermInterface[] $genres */
      $genres = [];

      $genres_uuid = $resource_validator->getGenresUuid();

      if ($genres_uuid) {
        foreach ($genres_uuid as $genre_uuid) {
          $genre = $this->termStorage->loadByProperties([
            'vid' => 'genre',
            'uuid' => $genre_uuid,
          ]);

          if (!$genre) {
            continue;
          }

          /** @var \Drupal\taxonomy\TermInterface $genre */
          $genre = reset($genre);
          $genres[] = $genre;
        }
      }

      $resource_validator->setGenres($genres);
    }

    return $resource_validator;
  }

  /**
   * Add a condition to filter games by genres UUID.
   *
   * When an element does not provide any Publish infos, they will not be
   * filter out.
   *
   * @param array $genres_uuid
   *   The collection of genres UUID to use for filtering.
   *
   * @return array
   *   The Nested OR-Condition query to filter-out games by genre.
   */
  private function addGenresFilter(array $genres_uuid): array {
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

    foreach ($genres_uuid as $genre_uuid) {
      $structure['nested']['query']['bool']['should'][] = ['term' => ['genres.uuid' => $genre_uuid]];
    }

    return $structure;
  }

  /**
   * Add a condition to filter games by platforms UUID.
   *
   * When an element does not provide any Publish infos, they will not be
   * filter out.
   *
   * @param array $platforms_uuid
   *   The collection of platforms UUID to use for filtering.
   *
   * @return array
   *   The Nested OR-Condition query to filter-out games by release platform .
   */
  private function addPlatformsFilter(array $platforms_uuid): array {
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

    foreach ($platforms_uuid as $platform_uuid) {
      $structure['nested']['query']['bool']['should'][] = ['term' => ['releases.platform_uuid' => $platform_uuid]];
    }

    return $structure;
  }

}
