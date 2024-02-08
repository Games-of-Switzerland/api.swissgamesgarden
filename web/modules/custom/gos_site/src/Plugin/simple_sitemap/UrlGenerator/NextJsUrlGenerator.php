<?php

namespace Drupal\gos_site\Plugin\simple_sitemap\UrlGenerator;

use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\gos_site\UrlBuilderNextJs;
use Drupal\simple_sitemap\Entity\EntityHelper;
use Drupal\simple_sitemap\Exception\SkipElementException;
use Drupal\simple_sitemap\Logger;
use Drupal\simple_sitemap\Manager\EntityManager;
use Drupal\simple_sitemap\Plugin\simple_sitemap\SimpleSitemapPluginBase;
use Drupal\simple_sitemap\Plugin\simple_sitemap\UrlGenerator\EntityUrlGenerator;
use Drupal\simple_sitemap\Plugin\simple_sitemap\UrlGenerator\UrlGeneratorManager;
use Drupal\simple_sitemap\Settings;
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
   * @psalm-suppress MissingParamType
   *
   * @SuppressWarnings(PHPMD.ExcessiveParameterList)
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Logger $logger,
    Settings $settings,
    LanguageManagerInterface $language_manager,
    EntityTypeManagerInterface $entity_type_manager,
    EntityHelper $entityHelper,
    EntityManager $entities_manager,
    UrlGeneratorManager $url_generator_manager,
    MemoryCacheInterface $memory_cache,
    UrlBuilderNextJs $url_builder_nextjs
  ) {
    parent::__construct(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $logger,
      $settings,
      $language_manager,
      $entity_type_manager,
      $entityHelper,
      $entities_manager,
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
   * @psalm-suppress UnsafeInstantiation
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ): SimpleSitemapPluginBase {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('simple_sitemap.logger'),
      $container->get('simple_sitemap.settings'),
      $container->get('language_manager'),
      $container->get('entity_type.manager'),
      $container->get('simple_sitemap.entity_helper'),
      $container->get('simple_sitemap.entity_manager'),
      $container->get('plugin.manager.simple_sitemap.url_generator'),
      $container->get('entity.memory_cache'),
      $container->get('gos_site.url_builder.nextjs')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getAlternateUrlsForTranslatedLanguages(ContentEntityInterface $entity, Url $url_object): array {
    $alternate_urls = [];

    /** @var \Drupal\Core\Language\Language $language */
    foreach ($entity->getTranslationLanguages() as $language) {
      if (!isset($this->settings->get('excluded_languages')[$language->getId()]) || $language->isDefault()) {
        if ($entity->getTranslation($language->getId())->access('view', $this->anonUser)) {
          $alternate_urls[$language->getId()] = $this->urlBuilderNextJs->buildUrl($entity, NULL, $language->getId());
        }
      }
    }

    return $alternate_urls;
  }

  /**
   * {@inheritdoc}
   */
  protected function getUrlVariants(array $path_data, Url $url_object): array {
    $url_variants = [];
    $alternate_urls = [];

    if (!$this->sitemap->isMultilingual() || !isset($path_data['meta']['entity'])) {
      $alternate_urls = $this->getAlternateUrlsForDefaultLanguage($url_object);
    }

    if ($this->sitemap->isMultilingual() && $path_data['meta']['entity'] instanceof ContentEntityInterface) {
      $alternate_urls = $this->getAlternateUrlsForTranslatedLanguages($path_data['meta']['entity'], $url_object);
    }

    foreach ($alternate_urls as $langcode => $url) {
      $url_variants[] = $path_data + [
        'langcode' => $langcode,
        'url' => $url,
        'alternate_urls' => $alternate_urls,
      ];
    }

    return $url_variants;
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress InvalidScalarArgument
   */
  protected function processEntity(ContentEntityInterface $entity): array {
    $sitemap_id = (string) $this->sitemap->id();

    if (empty($sitemap_id)) {
      throw new SkipElementException();
    }

    $entity_settings = $this->entitiesManager
      ->setSitemaps($sitemap_id)
      ->getEntityInstanceSettings($entity->getEntityTypeId(), $entity->id());

    if (empty($entity_settings[$sitemap_id]['index'])) {
      throw new SkipElementException();
    }

    $entity_settings = $entity_settings[$sitemap_id] ?? NULL;

    if (empty($entity_settings)) {
      throw new SkipElementException();
    }

    $url_object = $entity->toUrl()->setAbsolute();
    $path = $url_object->getInternalPath();

    // Do not include external paths.
    if (!$url_object->isRouted()) {
      throw new SkipElementException();
    }

    if ($entity->getEntityTypeId() === 'node' && \array_key_exists($entity->bundle(), $this->urlBuilderNextJs::NEXTJS_URLS_PREFIX)) {
      /** @var \Drupal\Core\Entity\ContentEntityBase $node */
      $node = $entity;
      $url_object = Url::fromUri($this->urlBuilderNextJs->buildUrl($node));
    }

    return [
      'url' => $url_object,
      'lastmod' => method_exists($entity, 'getChangedTime')
        ? date('c', $entity->getChangedTime())
        : NULL,
      'priority' => $entity_settings['priority'] ?? NULL,
      'changefreq' => !empty($entity_settings['changefreq']) ? $entity_settings['changefreq'] : NULL,
      'images' => !empty($entity_settings['include_images'])
        ? $this->getEntityImageData($entity)
        : [],

      // Additional info useful in hooks.
      'meta' => [
        'path' => $path,
        'entity' => $entity,
        'entity_info' => [
          'entity_type' => $entity->getEntityTypeId(),
          'id' => $entity->id(),
        ],
      ],
    ];
  }

}
