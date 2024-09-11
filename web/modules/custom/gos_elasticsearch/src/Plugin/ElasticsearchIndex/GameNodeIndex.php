<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

use Drupal\elasticsearch_helper\Elasticsearch\Index\FieldDefinition;
use Drupal\elasticsearch_helper\Elasticsearch\Index\MappingDefinition;

/**
 * A Node-Game content index class.
 *
 * @ElasticsearchIndex(
 *     id="gos_index_node_game",
 *     label=@Translation("Game Node Index"),
 *     indexName="{index_prefix}_gos_node_game_{langcode}",
 *     typeName="node",
 *     entityType="node"
 * )
 */
class GameNodeIndex extends NodeIndexBase {

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress UnusedForeachValue
   */
  public function setup(): void {
    // Create one index per language, so that we can have different analyzers.
    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      // Get index name.
      $index_name = $this->getIndexName(['langcode' => $langcode]);

      /**
       * @var \Elastic\Elasticsearch\Response\Elasticsearch $exists_response
       *
       * @psalm-suppress InvalidArgument
       */
      $exists_response = $this->client->indices()->exists(['index' => $index_name]);

      // Check if index exists.
      if ($exists_response->getStatusCode() !== 200) {
        // Get index definition.
        $index_definition = $this->getIndexDefinition(['langcode' => $langcode]);

        if ($index_definition === NULL) {
          return;
        }

        // Define multi-field title field.
        $title = FieldDefinition::create('text', [
          'analyzer' => 'game_title_analyzer',
          'search_analyzer' => 'game_title_analyzer_search',
        ])
          ->addMultiField('keyword', FieldDefinition::create('keyword'));

        // Add specific properties per index's language using specific analyzer
        // per-language.
        $index_definition->getMappingDefinition()
          ->addProperty('title', $title)
          ->addProperty('desc', FieldDefinition::create('text', [
            'analyzer' => 'english_language_analyzer',
          ]));

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

    if ($index_definition === NULL) {
      return NULL;
    }

    // Add custom settings.
    $index_definition->getSettingsDefinition()->addOptions([
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
          'people_fullname_filter' => [
            'type' => 'edge_ngram',
            'min_gram' => 2,
            'max_gram' => 50,
            'token_chars' => [
              'letter',
            ],
          ],
          'studio_name_filter' => [
            'type' => 'edge_ngram',
            'min_gram' => 3,
            'max_gram' => 128,
            'token_chars' => [
              'letter',
              'digit',
            ],
          ],
        ],
        'analyzer' => [
          'game_title_analyzer' => [
            'tokenizer' => 'game_title_tokenizer',
            'filter' => [
              'lowercase',
              'asciifolding',
            ],
          ],
          'game_title_analyzer_search' => [
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
            'filter' => [
              'lowercase',
              'synonym_platform_filter',
            ],
          ],
          'people_fullname_analyzer' => [
            'tokenizer' => 'standard',
            'filter' => [
              'lowercase',
              'people_fullname_filter',
              'asciifolding',
            ],
          ],
          'studio_name_analyzer' => [
            'tokenizer' => 'standard',
            'filter' => [
              'lowercase',
              'asciifolding',
              'studio_name_filter',
              'english_stemmer',
            ],
          ],
        ],
        'tokenizer' => [
          'game_title_tokenizer' => [
            'type' => 'edge_ngram',
            'min_gram' => 2,
            'max_gram' => 255,
            'token_chars' => [
              'letter',
              'digit',
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
    // Define nested players field.
    $players = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('min', FieldDefinition::create('short'))
      ->addProperty('max', FieldDefinition::create('short'));

    // Define nested releases field.
    $releases = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('date', FieldDefinition::create('date', [
        'format' => 'yyyy-MM-dd',
      ]))
      ->addProperty('platform_name', FieldDefinition::create('keyword'))
      ->addProperty('platform_slug', FieldDefinition::create('keyword'))
      ->addProperty('state', FieldDefinition::create('keyword'));

    // Define nested releases' states field.
    $releases_states = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('state', FieldDefinition::create('keyword'));

    // Define nested releases' years field.
    $releases_years = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('year', FieldDefinition::create('date', [
        'format' => 'yyyy',
      ]));

    // Define nested studios field.
    $studios = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('uuid', FieldDefinition::create('keyword', [
        'index' => FALSE,
      ]))
      ->addProperty('name', FieldDefinition::create('text', [
        'analyzer' => 'studio_name_analyzer',
      ]))
      ->addProperty('path', FieldDefinition::create('text', [
        'index' => FALSE,
      ]));

    // Define nested genres field.
    $genres = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('slug', FieldDefinition::create('keyword'))
      ->addProperty('name', FieldDefinition::create('keyword'));

    // Define nested locations field.
    $locations = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('slug', FieldDefinition::create('keyword'))
      ->addProperty('name', FieldDefinition::create('keyword'));

    // Define nested cantons field.
    $cantons = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('slug', FieldDefinition::create('keyword'))
      ->addProperty('name', FieldDefinition::create('keyword'));

    // Define nested stores field.
    $stores = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('slug', FieldDefinition::create('keyword'))
      ->addProperty('name', FieldDefinition::create('keyword'))
      ->addProperty('link', FieldDefinition::create('text', [
        'index' => FALSE,
      ]));

    // Define nested people field.
    $people = FieldDefinition::create('nested', [
      'dynamic' => FALSE,
    ])
      ->addProperty('uuid', FieldDefinition::create('keyword', [
        'index' => FALSE,
      ]))
      ->addProperty('fullname', FieldDefinition::create('text', [
        'analyzer' => 'people_fullname_analyzer',
      ]))
      ->addProperty('path', FieldDefinition::create('text', [
        'index' => FALSE,
      ]));

    // Create here only properties that are not affected by language analyzer.
    return MappingDefinition::create()
      ->addProperty('uuid', FieldDefinition::create('keyword'))
      ->addProperty('is_published', FieldDefinition::create('boolean'))
      ->addProperty('path', FieldDefinition::create('text', [
        'index' => FALSE,
      ]))
      ->addProperty('bundle', FieldDefinition::create('keyword'))
      ->addProperty('players', $players)
      ->addProperty('releases', $releases)
      ->addProperty('releases_states', $releases_states)
      ->addProperty('releases_years', $releases_years)
      ->addProperty('studios', $studios)
      ->addProperty('genres', $genres)
      ->addProperty('locations', $locations)
      ->addProperty('cantons', $cantons)
      ->addProperty('stores', $stores)
      ->addProperty('people', $people)
      ->addProperty('changed', FieldDefinition::create('date', [
        'format' => 'epoch_second',
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

    // Only Index Game.
    if ($entity->bundle() !== 'game') {
      return;
    }

    parent::index($entity);
  }

}
