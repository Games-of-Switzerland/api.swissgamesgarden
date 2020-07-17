<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

/**
 * A Node-People content index class.
 *
 * @ElasticsearchIndex(
 *     id="gos_index_node_people",
 *     label=@Translation("People Node Index"),
 *     indexName="{index_prefix}_gos_node_people_{langcode}",
 *     typeName="node",
 *     entityType="node"
 * )
 */
class PeopleNodeIndex extends NodeIndexBase {

  /**
   * {@inheritdoc}
   */
  public function setup(): void {
    // Create one index per language, so that we can have different analyzers.
    parent::setup();

    // Create one index per language, so that we can have different analyzers.
    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      $index_name = $this->getIndexName(['langcode' => $langcode]);

      // Close the index before setting configuration.
      $this->client->indices()->close(['index' => $index_name]);

      $settings = [
        'index' => $this->indexNamePattern(),
        'body' => [
          'analysis' => ['filter' => [], 'analyzer' => [], 'tokenizer' => []],
        ],
      ];
      $settings['body']['analysis']['filter'] = [
        'people_fullname_filter' => [
          'type' => 'edge_ngram',
          'min_gram' => 2,
          'max_gram' => 10,
          'token_chars' => [
            'letter',
          ],
        ],
      ];
      $settings['body']['analysis']['analyzer'] = [
        'people_fullname_analyzer' => [
          'tokenizer' => 'standard',
          'filter' => [
            'lowercase',
            'people_fullname_filter',
            'asciifolding',
          ],
        ],
      ];
      $this->client->indices()->putSettings($settings);

      $mapping = [
        'index' => $this->indexNamePattern(),
        'type' => $this->typeNamePattern(),
        'body' => [
          'properties' => [
            'uuid' => [
              'type' => 'keyword',
            ],
            'is_published' => [
              'type' => 'boolean',
            ],
            'fullname' => [
              'type' => 'text',
              'analyzer' => 'people_fullname_analyzer',
            ],
            'path' => [
              'type' => 'text',
              'index' => FALSE,
            ],
            'bundle' => [
              'type' => 'keyword',
            ],
          ],
        ],
      ];
      $this->client->indices()->putMapping($mapping);

      // Re-open the index to make to expose it.
      $this->client->indices()->open(['index' => $index_name]);
    }
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress InvalidArgument
   */
  public function index($source): void {
    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $source;

    // Only Index People.
    if ($entity->bundle() !== 'people') {
      return;
    }

    parent::index($entity);
  }

}
