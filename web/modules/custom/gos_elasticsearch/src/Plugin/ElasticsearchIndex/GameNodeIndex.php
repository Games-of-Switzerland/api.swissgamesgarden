<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

use Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexBase;

/**
 * A Node-Game content index class.
 *
 * @ElasticsearchIndex(
 *   id = "gos_index_node_game",
 *   label = @Translation("Game Node Index"),
 *   indexName = "gos_node_game",
 *   typeName = "node",
 *   entityType = "node"
 * )
 */
class GameNodeIndex extends ElasticsearchIndexBase {

  /**
   * {@inheritdoc}
   */
  public function index($source) {
    /** @var \Drupal\node\Entity\NodeInterface $source */

    // Only Index Game.
    if ($source->bundle() !== 'game') {
      return NULL;
    }

    // Skip unpublished game.
    if (!$source->isPublished()) {
      return NULL;
    }

    parent::index($source);
  }

  /**
   * {@inheritdoc}
   */
  public function setup() {
    // Close the indice before setting configuration.
    $this->client->indices()->close(['index' => $this->indexNamePattern()]);

    $settings = [
      'index' => $this->indexNamePattern(),
      'body' => [
        'analysis' => [
          'filter' => [
            'synonym_platform_filter' => [
              'type' => 'synonym_graph',
              'synonyms_path' => 'analysis/synonym_platform.txt',
            ],
          ],
          'analyzer' => [
            'synonym_platform_analyzer' => [
              'tokenizer' => 'standard',
              'filter' => ['standard', 'lowercase', 'synonym_platform_filter'],
            ],
          ],
        ],
      ],
    ];
    $this->client->indices()->putSettings($settings);

    $mapping = [
      'index' => $this->indexNamePattern(),
      'type' => $this->typeNamePattern(),
      'body' => [
        'properties' => [
          'nid' => [
            'type' => 'integer',
            'index' => FALSE,
          ],
          'title' => [
            'type' => 'text',
          ],
          'releases' => [
            'type' => 'nested',
            'dynamic' => FALSE,
            'properties' => [
              'date' => [
                'type' => 'date',
                'format' => 'yyyy-MM-dd',
              ],
              'platform' => [
                'type' => 'text',
                'analyzer' => 'synonym_platform_analyzer',
              ],
            ],
          ],
        ],
      ],
    ];
    $this->client->indices()->putMapping($mapping);

    // Re-open the indice to make to expose it.
    $this->client->indices()->open(['index' => $this->indexNamePattern()]);
  }

}
