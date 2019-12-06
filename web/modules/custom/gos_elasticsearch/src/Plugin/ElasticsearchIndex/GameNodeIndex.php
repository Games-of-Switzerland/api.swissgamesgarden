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
    $mapping = [
      'index' => $this->indexNamePattern(),
      // Type name should match the Annotation @typeName.
      'type' => $this->typeNamePattern(),
      'body' => [
        'properties' => [
          'nid' => [
            'type' => 'integer',
          ],
          'title' => [
            'type' => 'text',
          ],
        ],
      ],
    ];

    $this->client->indices()->putMapping($mapping);
  }

}
