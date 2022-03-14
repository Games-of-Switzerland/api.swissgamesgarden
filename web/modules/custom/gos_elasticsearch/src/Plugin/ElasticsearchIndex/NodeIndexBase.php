<?php

namespace Drupal\gos_elasticsearch\Plugin\ElasticsearchIndex;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Site\Settings;
use Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexBase;
use Elasticsearch\Client;
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
  public const SETTINGS_INDEX_PREFIX = 'gos_elasticsearch.index_prefix';

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $client, Serializer $serializer, LoggerInterface $logger, MessengerInterface $messenger, LanguageManagerInterface $languageManager, Settings $settings) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $client, $serializer, $logger, $messenger);
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
   * @psalm-suppress UnsafeInstantiation
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('elasticsearch_helper.elasticsearch_client'),
      $container->get('serializer'),
      $container->get('logger.factory')->get('elasticsearch_helper'),
      $container->get('messenger'),
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
      throw new \InvalidArgumentException('No index prefix was specified in settings.php.');
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

    parent::index($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function indexNamePattern() {
    if (!$this->settings::get(self::SETTINGS_INDEX_PREFIX)) {
      throw new \InvalidArgumentException('No index prefix was specified in settings.php.');
    }

    // Always specify the placeholder `index_prefix`.
    $index_prefix = $this->settings::get(self::SETTINGS_INDEX_PREFIX);
    $index_name = str_replace('{index_prefix}', $index_prefix, $this->pluginDefinition['indexName']);

    return preg_replace($this->placeholder_regex, '*', $index_name);
  }

}
