<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

/**
 * A Node-Studio content index class.
 *
 * @ElasticsearchIndex(
 *     id="gos_index_node_studio",
 *     label=@Translation("Studio Node Index"),
 *     indexName="{index_prefix}_gos_node_studio_{langcode}",
 *     typeName="node",
 *     entityType="node"
 * )
 */
class StudioNodeIndex extends NodeIndexBase {

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
        'english_stemmer' => [
          'type' => 'stemmer',
          'language' => 'english',
        ],
        'studio_name_filter' => [
          'type' => 'edge_ngram',
          'min_gram' => 2,
          'max_gram' => 10,
          'token_chars' => [
            'letter',
            'digit',
          ],
        ],
      ];
      $settings['body']['analysis']['analyzer'] = [
        'studio_name_analyzer' => [
          'tokenizer' => 'standard',
          'filter' => [
            'lowercase',
            'asciifolding',
            'studio_name_filter',
            'english_stemmer',
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
              'analyzer' => 'studio_name_analyzer',
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

    // Only Index Studio.
    if ($entity->bundle() !== 'studio') {
      return;
    }

    parent::index($entity);
  }

}
