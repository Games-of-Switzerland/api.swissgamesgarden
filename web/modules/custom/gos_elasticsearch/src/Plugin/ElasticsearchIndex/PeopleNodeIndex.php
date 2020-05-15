<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

/**
 * A Node-People content index class.
 *
 * @ElasticsearchIndex(
 *   id = "gos_index_node_people",
 *   label = @Translation("People Node Index"),
 *   indexName="{index_prefix}_gos_node_people_{langcode}",
 *   typeName = "node",
 *   entityType = "node"
 * )
 */
class PeopleNodeIndex extends NodeIndexBase {

  /**
   * {@inheritdoc}
   */
  public function index($source) {
    /** @var \Drupal\node\Entity\NodeInterface $source */

    // Only Index People.
    if ($source->bundle() !== 'people') {
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
  public function setup(): void {
    // Create one index per language, so that we can have different analyzers.
    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      $index_name = $this->getIndexName(['langcode' => $langcode]);

      if (!$this->client->indices()->exists(['index' => $index_name])) {
        $this->client->indices()->create([
          'index' => $index_name,
          'body'  => [
            'number_of_shards'   => 1,
            'number_of_replicas' => 0,
          ],
        ]);

        $this->logger->notice('Message: Index @index has been created.', [
          '@index' => $index_name,
        ]);
      }

      // Close the index before setting configuration.
      $this->client->indices()->close(['index' => $index_name]);

      $settings = [
        'index' => $this->indexNamePattern(),
        'body'  => [
          'analysis' => [
            'filter'    => [
              'metaphone_filter' => [
                'type'        => 'phonetic',
                'encoder'     => 'beider_morse',
                'replace'     => FALSE,
                'languageset' => [
                  'english',
                ],
              ],
            ],
            'analyzer'  => [
              'ngram_analyzer_search'  => [
                'tokenizer' => 'lowercase',
              ],
              'phonetic_name_analyzer' => [
                'tokenizer' => 'standard',
                'filter'    => [
                  'lowercase',
                  'metaphone_filter',
                ],
              ],
            ],
            'tokenizer' => [
              'ngram_analyzer_tokenizer' => [
                'type'        => 'edge_ngram',
                'min_gram'    => 2,
                'max_gram'    => 10,
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
        'type'  => $this->typeNamePattern(),
        'body'  => [
          'properties' => [
            'nid'      => [
              'type'  => 'integer',
              'index' => FALSE,
            ],
            'fullname' => [
              'type'            => 'text',
              'analyzer'        => 'phonetic_name_analyzer',
              'search_analyzer' => 'ngram_analyzer_search',
            ],
          ],
        ],
      ];
      $this->client->indices()->putMapping($mapping);

      // Re-open the index to make to expose it.
      $this->client->indices()->open(['index' => $index_name]);
    }
  }

}
