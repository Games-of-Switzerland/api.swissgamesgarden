<?php

namespace Drupal\gos_site\Plugin\simple_sitemap\UrlGenerator;

use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\gos_site\UrlBuilderNextJs;
use Drupal\simple_sitemap\EntityHelper;
use Drupal\simple_sitemap\Logger;
use Drupal\simple_sitemap\Plugin\simple_sitemap\UrlGenerator\EntityUrlGenerator;
use Drupal\simple_sitemap\Plugin\simple_sitemap\UrlGenerator\UrlGeneratorManager;
use Drupal\simple_sitemap\Simplesitemap;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NextUrlGenerator.
 *
 * @UrlGenerator(
 *     id="nextjs",
 *     label=@Translation("NextJS URL generator"),
 *     description=@Translation("Generates URLs for NextJS."),
 * )
 */
class NextJsUrlGenerator extends EntityUrlGenerator {

  /**
   * The NextJs URL Builder.
   *
   * @var \Drupal\gos_site\UrlBuilderNextJs
   */
  protected $urlBuilderNextJs;

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress PropertyTypeCoercion
   * @psalm-suppress MissingParamType
   *
   * @SuppressWarnings(PHPMD.ExcessiveParameterList)
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Simplesitemap $generator,
    Logger $logger,
    LanguageManagerInterface $language_manager,
    EntityTypeManagerInterface $entity_type_manager,
    EntityHelper $entityHelper,
    UrlGeneratorManager $url_generator_manager,
    MemoryCacheInterface $memory_cache,
    UrlBuilderNextJS $url_builder_nextjs
  ) {
    parent::__construct(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $generator,
      $logger,
      $language_manager,
      $entity_type_manager,
      $entityHelper,
      $url_generator_manager,
      $memory_cache
    );
    $this->urlBuilderNextJs = $url_builder_nextjs;
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress PossiblyNullArgument
   * @psalm-suppress ArgumentTypeCoercion
   * @psalm-suppress PossiblyNullReference
   * @psalm-suppress MissingParamType
   * @psalm-suppress UnsafeInstantiation
   * @psalm-suppress UnsafeInstantiation
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('simple_sitemap.generator'),
      $container->get('simple_sitemap.logger'),
      $container->get('language_manager'),
      $container->get('entity_type.manager'),
      $container->get('simple_sitemap.entity_helper'),
      $container->get('plugin.manager.simple_sitemap.url_generator'),
      $container->get('entity.memory_cache'),
      $container->get('gos_site.url_builder.nextjs')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  public function generate($data_set) {
    $path_data_sets = $this->processDataSet($data_set);
    $url_variant_sets = [];

    foreach ($path_data_sets as $path_data) {
      if (isset($path_data['meta']['entity']) && $path_data['meta']['entity'] instanceof ContentEntityInterface) {
        $url_object = $path_data['meta']['entity']->toUrl();
        unset($path_data['url']);
        $url_variant_sets[] = $this->getUrlVariants($path_data, $url_object);
      }
    }

    // Make sure to clear entity memory cache so it does not build up resulting
    // in a constant increase of memory.
    // See https://www.drupal.org/project/simple_sitemap/issues/3170261 and
    // https://www.drupal.org/project/simple_sitemap/issues/3202233
    $definition = $this->entityTypeManager->getDefinition($data_set['entity_type']);

    if ($definition && $definition->isStaticallyCacheable()) {
      $this->entityMemoryCache->deleteAll();
    }

    return array_merge([], ...$url_variant_sets);
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress InvalidArgument
   */
  protected function getAlternateUrlsForTranslatedLanguages(ContentEntityInterface $entity, Url $url_object): array {
    $alternate_urls = [];

    /** @var \Drupal\Core\Language\Language $language */
    foreach ($entity->getTranslationLanguages() as $language) {
      if (!isset($this->settings['excluded_languages'][$language->getId()]) || $language->isDefault()) {
        if ($entity->hasTranslation($language->getId()) && $entity->getTranslation($language->getId())->access('view', $this->anonUser)) {
          $alternate_urls[$language->getId()] = $this->urlBuilderNextJs->buildUrl($entity, NULL, $language->getId());
        }
      }
    }

    return $alternate_urls;
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress MissingParamType
   */
  protected function processDataSet($data_set) {
    $entities = $this->entityTypeManager->getStorage($data_set['entity_type'])->loadMultiple((array) $data_set['id']);

    if (empty($entities)) {
      return FALSE;
    }

    $paths = [];

    foreach ($entities as $entity) {
      $entity_id = (string) $entity->id();
      $entity_type_name = $entity->getEntityTypeId();
      $entity_bundle = $entity->bundle();

      $entity_settings = $this->generator
        ->setVariants($this->sitemapVariant)
        ->getEntityInstanceSettings($entity_type_name, $entity_id);

      if (empty($entity_settings['index'])) {
        continue;
      }

      $url_object = $entity->toUrl();

      // Do not include external paths.
      if (!$url_object->isRouted()) {
        continue;
      }

      $path = $url_object->getInternalPath();
      $url_object->setOption('absolute', TRUE);

      $url = $url_object->toString();

      if ($data_set['entity_type'] === 'node' && \array_key_exists($entity_bundle, $this->urlBuilderNextJs::NEXTJS_URLS_PREFIX)) {
        /** @var \Drupal\Core\Entity\ContentEntityBase $node */
        $node = $entity;
        $url = $this->urlBuilderNextJs->buildUrl($node);
      }

      $paths[] = [
        'url' => $url,
        'lastmod' => method_exists($entity, 'getChangedTime') ? date('c', $entity->getChangedTime()) : NULL,
        'priority' => $entity_settings['priority'] ?? NULL,
        'changefreq' => $entity_settings['changefreq'] ?? NULL,

        // Additional info useful in hooks.
        'meta' => [
          'path' => $path,
          'entity' => $entity,
          'entity_info' => [
            'entity_type' => $entity_type_name,
            'id' => $entity_id,
          ],
        ],
      ];
    }

    return $paths;
  }

}
