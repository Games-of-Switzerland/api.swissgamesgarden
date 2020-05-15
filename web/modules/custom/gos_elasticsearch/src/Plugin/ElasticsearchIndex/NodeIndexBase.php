<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexBase;
use Elasticsearch\Client;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Abstract class to create Node index.
 */
abstract class NodeIndexBase extends ElasticsearchIndexBase {

  /**
   * Setting used to add a prefix for ES index based on the environment.
   *
   * @var string
   */
  const SETTINGS_INDEX_PREFIX = 'gos_elasticsearch.index_prefix';

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The site settings.
   *
   * @var \Drupal\Core\Site\Settings
   */
  protected $settings;

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress MissingParamType
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $client, Serializer $serializer, LoggerInterface $logger, LanguageManagerInterface $languageManager, Settings $settings) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $client, $serializer, $logger);

    $this->languageManager = $languageManager;
    $this->settings = $settings;
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
          'body' => [
            'number_of_shards' => 1,
            'number_of_replicas' => 0,
          ],
        ]);

        $this->logger->notice('Message: Index @index has been created.', [
          '@index' => $index_name,
        ]);
      }
    }
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress ArgumentTypeCoercion
   * @psalm-suppress PossiblyNullArgument
   * @psalm-suppress PossiblyNullReference
   * @psalm-suppress MissingParamType
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('elasticsearch_helper.elasticsearch_client'),
      $container->get('serializer'),
      $container->get('logger.factory')->get('elasticsearch_helper'),
      $container->get('language_manager'),
      $container->get('settings')
    );
  }

  /**
   * Return the index name as it is not public by default.
   *
   * @param mixed $data
   *   Will be used to build the index name by replacing token with values.
   *
   * @return string
   *   The index name.
   *
   * @psalm-suppress MethodSignatureMismatch
   */
  public function getIndexName($data): string {
    if (!$this->settings::get(self::SETTINGS_INDEX_PREFIX)) {
      throw new InvalidArgumentException('No index prefix was specified in settings.php.');
    }

    // Always specify the placeholder `index_prefix`.
    $data['index_prefix'] = $this->settings::get(self::SETTINGS_INDEX_PREFIX);

    if (!\array_key_exists('langcode', $data)) {
      $data['langcode'] = $this->languageManager->getCurrentLanguage()->getId();
    }

    return parent::getIndexName($data);
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress InvalidArgument
   */
  public function index($source): void {
    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $source;

    // Skip unpublished people.
    if (!$entity->isPublished()) {
      return;
    }

    parent::index($entity);
  }

  /**
   * Get the standardized Noun & Title analyzer.
   *
   * @return array
   *   the Elasticsearch array structure to build title and noun analyzers.
   */
  protected function getAnalyzersTitleAndNoun(): array {
    return [
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
    ];
  }

  /**
   * Get the standardized Noun & Title filter.
   *
   * @return array
   *   the Elasticsearch array structure to build title and noun filters.
   */
  protected function getFiltersTitleAndNoun(): array {
    return [
      'metaphone_filter' => [
        'type' => 'phonetic',
        'encoder' => 'beider_morse',
        'replace' => FALSE,
        'languageset' => [
          'english',
        ],
      ],
    ];
  }

  /**
   * Get the standardized Noun & Title tokenizers.
   *
   * @return array
   *   the Elasticsearch array structure to build title and noun tokenizers.
   */
  protected function getTokenizersTitleAndNoun(): array {
    return [
      'ngram_analyzer_tokenizer' => [
        'type' => 'edge_ngram',
        'min_gram' => 2,
        'max_gram' => 10,
        'token_chars' => [
          'letter',
          'digit',
        ],
      ],
    ];
  }

}
