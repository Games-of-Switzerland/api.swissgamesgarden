<?php

namespace Drupal\gos_elasticsearch\Plugin\rest\resource;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexManager;
use Drupal\gos_rest\Plugin\rest\ValidatorFactory;
use Elasticsearch\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a Proxy to access to Elasticsearch Games/People/Studio Documents.
 *
 * Search thought multiple content (games, people, studio, ...).
 * The response will always return a maximum of 5 items per type.
 *
 * @RestResource(
 *     id="elasticsearch_autocomplete_resource",
 *     label=@Translation("Proxy to access to Elasticsearch Games/People/Studio Documents as Autocomplete"),
 *     uri_paths={
 *         "canonical": "/autocomplete"
 *     }
 * )
 */
class ElasticAutocompleteResource extends ElasticResourceBase {

  /**
   * The maximum element by bundle returned for a response.
   *
   * @var string
   */
  public const PAGER_SIZE = 5;

  /**
   * The Elasticsearch client.
   *
   * @var \Elasticsearch\Client
   */
  protected $client;

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
    Client $client
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger, $validator_factory, $elasticsearch_plugin_manager);
    $this->client = $client;
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
      $container->get('elasticsearch_helper.elasticsearch_client')
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

    /** @var \Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex\GameNodeIndex $index_games */
    $index_games = $this->elasticsearchPluginManager->createInstance('gos_index_node_game');
    /** @var \Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex\PeopleNodeIndex $index_people */
    $index_people = $this->elasticsearchPluginManager->createInstance('gos_index_node_people');
    /** @var \Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex\StudioNodeIndex $index_studio */
    $index_studio = $this->elasticsearchPluginManager->createInstance('gos_index_node_studio');

    $es_query = [
      'index' => [
        $index_games->getIndexName([]),
        $index_people->getIndexName([]),
        $index_studio->getIndexName([]),
      ],
      'from' => 0,
      'size' => 0,
      'body' => [
        'query' => [
          'bool' => [
            'must' => [
              // Where all the conditions modifying the Score should be added.
            ],
            'filter' => [
              'bool' => [
                // Where all the conditions without a Score impact should be.
                'must' => [
                  // Get only published entities.
                  [
                    'term' => [
                      'is_published' => TRUE,
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
        'aggs' => [
          'bundles' => [
            'filter' => [
              'bool' => [
                'should' => [
                  [
                    'term' => [
                      'bundle' => 'game',
                    ],
                  ],
                  [
                    'term' => [
                      'bundle' => 'studio',
                    ],
                  ],
                  [
                    'term' => [
                      'bundle' => 'people',
                    ],
                  ],
                ],
              ],
            ],
            'aggs' => [
              'bundle' => [
                'terms' => [
                  'field' => 'bundle',
                ],
                'aggs' => [
                  'top' => [
                    'top_hits' => [
                      'size' => self::PAGER_SIZE,
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
    ];

    $search = $request->query->get('q');
    $search = Xss::filter($search);

    if ($search) {
      $es_query['body']['query']['bool']['must'][]['multi_match'] = $this->addFullTextCondition($search);
    }

    try {
      $results = $this->client->search($es_query);
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
   * Add a full-text condition to query.
   *
   * @param string $search
   *   The keywords to filter by.
   *
   * @return array
   *   The condition query to filter-out by keyword on content.
   */
  private function addFullTextCondition(string $search): array {
    return [
      'query' => $search,
      'fields' => ['title', 'fullname', 'name'],
      'operator' => 'or',
      'fuzziness' => 1,
    ];
  }

}
