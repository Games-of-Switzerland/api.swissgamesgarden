<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

use Drupal\elasticsearch_helper\Elasticsearch\Index\FieldDefinition;
use Drupal\elasticsearch_helper\Elasticsearch\Index\MappingDefinition;

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
    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      // Get index name.
      $index_name = $this->getIndexName(['langcode' => $langcode]);

      /** @var \Elastic\Elasticsearch\Response\Elasticsearch $exists_response */
      $exists_response = $this->client->indices()->exists(['index' => $index_name]);

      // Check if index exists.
      if ($exists_response->getStatusCode() !== 200) {
        // Get index definition.
        $index_definition = $this->getIndexDefinition(['langcode' => $langcode]);

        if (!$index_definition) {
          return;
        }

        // Add specific properties per index's language using specific analyzer
        // per-language.
        $index_definition->getMappingDefinition();

        $this->createIndex($index_name, $index_definition);

        $this->logger->notice('Message: Index @index has been created.', [
          '@index' => $index_name,
        ]);
      }
      else {
        $this->logger->notice('Message: Index @index already exists.', [
          '@index' => $index_name,
        ]);
      }

      $this->logger->notice('Message: Something went wrong when contacting @index.', [
        '@index' => $index_name,
      ]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getIndexDefinition(array $context = []) {
    // Get index definition.
    $index_definition = parent::getIndexDefinition($context);

    if (!$index_definition) {
      return NULL;
    }

    // Add custom settings.
    $index_definition->getSettingsDefinition()->addOptions([
      'analysis' => [
        'filter' => [
          'people_fullname_filter' => [
            'type' => 'edge_ngram',
            'min_gram' => 2,
            'max_gram' => 10,
            'token_chars' => [
              'letter',
            ],
          ],
        ],
        'analyzer' => [
          'people_fullname_analyzer' => [
            'tokenizer' => 'standard',
            'filter' => [
              'lowercase',
              'people_fullname_filter',
              'asciifolding',
            ],
          ],
        ],
      ],
    ]);

    return $index_definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getMappingDefinition(array $context = []) {
    // Create here only properties that are not affected by language analyzer.
    return MappingDefinition::create()
      ->addProperty('uuid', FieldDefinition::create('keyword'))
      ->addProperty('is_published', FieldDefinition::create('boolean'))
      ->addProperty('path', FieldDefinition::create('text', [
        'index' => FALSE,
      ]))
      ->addProperty('bundle', FieldDefinition::create('keyword'))
      ->addProperty('fullname', FieldDefinition::create('text', [
        'analyzer' => 'people_fullname_analyzer',
      ]));
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
