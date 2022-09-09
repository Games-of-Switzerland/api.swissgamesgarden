<?php

namespace Drupal\gos_elasticsearch\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexManager;
use Drupal\gos_rest\Plugin\rest\resource\ResourceCacheableResponseTrait;
use Drupal\gos_rest\Plugin\rest\resource\ResourceValidationTrait;
use Drupal\gos_rest\Plugin\rest\ValidatorFactory;
use Drupal\rest\Plugin\ResourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Common base class for Elasticsearch bridge plugins.
 */
abstract class ElasticResourceBase extends ResourceBase {
  use ResourceCacheableResponseTrait;
  use ResourceValidationTrait;

  /**
   * The Elasticsearch Helper index plugin manager.
   *
   * @var \Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexManager
   */
  protected $elasticsearchPluginManager;

  /**
   * Settings manager.
   *
   * @var \Drupal\Core\Site\Settings
   */
  protected $settings;

  /**
   * Constructs a ElasticPropertiesResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   A logger instance.
   * @param \Drupal\gos_rest\Plugin\rest\ValidatorFactory $validator_factory
   *   The Validator factory to get proper validator.
   * @param \Drupal\elasticsearch_helper\Plugin\ElasticsearchIndexManager $elasticsearch_plugin_manager
   *   The Elasticsearch Helper index plugin manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerChannelInterface $logger,
    ValidatorFactory $validator_factory,
    ElasticsearchIndexManager $elasticsearch_plugin_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->validator = $validator_factory->getValidator();
    $this->elasticsearchPluginManager = $elasticsearch_plugin_manager;
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress PossiblyNullArgument
   * @psalm-suppress ArgumentTypeCoercion
   * @psalm-suppress PossiblyNullReference
   * @psalm-suppress UnsafeInstantiation
   * @psalm-suppress PossiblyInvalidArgument
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('gos_rest.validator_factory'),
      $container->get('plugin.manager.elasticsearch_index.processor')
    );
  }

  /**
   * Setup the base response & cacheable-metadata.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The incoming request.
   *
   * @return \Drupal\Core\Cache\CacheableJsonResponse
   *   The Json response.
   */
  public function get(Request $request): CacheableJsonResponse {
    /** @var \Drupal\Core\Cache\CacheableJsonResponse $response */
    $this->initResponse();
    $this->initCacheability();
    $this->response->addCacheableDependency($this->responseCache);

    return $this->response;
  }

  /**
   * Build a normalized error response when Elasticsearch does not works.
   *
   * @param \Exception $exception
   *   The elasticsearch exception.
   *
   * @return \Drupal\Core\Cache\CacheableJsonResponse
   *   The basic cacheable error response.
   *
   * @psalm-suppress UndefinedInterfaceMethod
   */
  protected function buildElasticsearchErrorResponse(\Exception $exception): CacheableJsonResponse {
    $this->response->setStatusCode(500);
    $this->response->setData([
      'message' => $this->t('Something went wrong with Elasticsearch.'),
      'errors' => [$exception->getMessage()],
    ]);

    return $this->response;
  }

}
