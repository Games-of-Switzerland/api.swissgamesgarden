<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

use Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexBase;

/**
 * A Node-Studio content index class.
 *
 * @ElasticsearchIndex(
 *   id = "gos_index_node_studio",
 *   label = @Translation("Studio Node Index"),
 *   indexName = "gos_node_studio",
 *   typeName = "node",
 *   entityType = "node"
 * )
 */
class StudioNodeIndex extends ElasticsearchIndexBase {

  /**
   * {@inheritdoc}
   */
  public function index($source) {
    /** @var \Drupal\node\Entity\NodeInterface $source */

    // Only Index Game.
    if ($source->bundle() !== 'studio') {
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
            'metaphone_filter' => [
              'type' => 'phonetic',
              'encoder' => 'beider_morse',
              'replace' => FALSE,
              'languageset' => [
                'english',
              ],
            ],
          ],
          'analyzer' => [
            'ngram_analyzer' => [
              'tokenizer' => 'ngram_analyzer_tokenizer',
              'filter' => ['lowercase'],
            ],
            'ngram_analyzer_search' => [
              'tokenizer' => 'lowercase',
            ],
            'phonetic_name_analyzer' => [
              'tokenizer' => 'standard',
              'filter' => [
                'lowercase',
                'metaphone_filter',
              ],
            ],
          ],
          'tokenizer' => [
            'ngram_analyzer_tokenizer' => [
              'type' => 'edge_ngram',
              'min_gram' => 2,
              'max_gram' => 10,
              'token_chars' => [
                'letter',
                'digit',
              ],
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
          'name' => [
            'type' => 'text',
            'analyzer' => 'phonetic_name_analyzer',
            'search_analyzer' => 'ngram_analyzer_search',
          ],
          'members' => [
            'type' => 'nested',
            'dynamic' => FALSE,
            'properties' => [
              'fullname' => [
                'type' => 'text',
                'analyzer' => 'phonetic_name_analyzer',
              ],
              'role' => [
                'type' => 'text',
                'analyzer' => 'ngram_analyzer',
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
