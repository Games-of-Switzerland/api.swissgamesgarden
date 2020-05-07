<?php

namespace Drupal\gos_elasticsearch\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableJsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a Proxy to access to Elasticsearch Games Documents.
 *
 * @RestResource(
 *   id="elasticsearch_games_resource",
 *   label=@Translation("Proxy to access to Elasticsearch Games Documents"),
 *   uri_paths={
 *     "canonical": "/search/games"
 *   }
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

    /** @var \Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex\GameNodeIndex $index */
    $index = $this->elasticsearchPluginManager->createInstance(self::ELASTICSEARCH_PLUGIN_ID);

    $current_page = $request->get('page') ?? 0;
    $query = [
      'index' => $index->getIndexName([]),
      'from' => $current_page * self::PAGER_SIZE,
      'size' => self::PAGER_SIZE,
    ];

    try {
      $results = $index->search($query);
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

}
