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
            'english_stop' => [
              'type' => 'stop',
              'stopwords' => '_english_',
            ],
            'english_stemmer' => [
              'type' => 'stemmer',
              'language' => 'english',
            ],
            'english_possessive_stemmer' => [
              'type' => 'stemmer',
              'language' => 'possessive_english',
            ],
            'synonym_platform_filter' => [
              'type' => 'synonym_graph',
              'synonyms_path' => 'analysis/synonym_platform.txt',
            ],
          ],
          'analyzer' => [
            'ngram_gametitle_analyzer' => [
              'tokenizer' => 'ngram_gametitle_tokenizer',
              'filter' => ['lowercase'],
            ],
            'ngram_gametitle_analyzer_search' => [
              'tokenizer' => 'lowercase',
            ],
            'english_language_analyzer' => [
              'tokenizer' => 'standard',
              'filter' => [
                'english_possessive_stemmer',
                'lowercase',
                'english_stop',
                'english_stemmer',
              ],
            ],
            'synonym_platform_analyzer' => [
              'tokenizer' => 'standard',
              'filter' => ['standard', 'lowercase', 'synonym_platform_filter'],
            ],
          ],
          'tokenizer' => [
            'ngram_gametitle_tokenizer' => [
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
          'title' => [
            'type' => 'text',
            'analyzer' => 'ngram_gametitle_analyzer',
            'search_analyzer' => 'ngram_gametitle_analyzer_search',
          ],
          'desc' => [
            'type' => 'text',
            'analyzer' => 'english_language_analyzer',
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
          'studios' => [
            'type' => 'nested',
            'dynamic' => FALSE,
            'properties' => [
              'name' => [
                'type' => 'text',
                'analyzer' => 'english_language_analyzer',
              ],
              'id' => [
                'type' => 'integer',
                'index' => FALSE,
              ],
            ],
          ],
          'genres' => [
            'type' => 'nested',
            'dynamic' => FALSE,
            'properties' => [
              'name' => [
                'type' => 'text',
                'analyzer' => 'english_language_analyzer',
              ],
              'id' => [
                'type' => 'integer',
                'index' => FALSE,
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
