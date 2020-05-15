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
      $settings['body']['analysis']['filter'] = array_merge($settings['body']['analysis']['filter'], $this->getFiltersTitleAndNoun());
      $settings['body']['analysis']['tokenizer'] = array_merge($settings['body']['analysis']['tokenizer'], $this->getTokenizersTitleAndNoun());
      $settings['body']['analysis']['analyzer'] = array_merge($settings['body']['analysis']['analyzer'], $this->getAnalyzersTitleAndNoun());
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
