<?php

namespace Drupal\gos_site;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Build URL from Drupal entity to Now.sh.
 */
class UrlBuilderNextJs {

  /**
   * NextJS URL patterns prefix to be used before the Drupal slug.
   *
   * @var array
   */
  public const array NEXTJS_URLS_PREFIX = [
    'game' => [
      'fr' => '',
      'en' => '',
      'de' => '',
    ],
    'studio' => [
      'fr' => '',
      'en' => '',
      'de' => '',
    ],
    'people' => [
      'fr' => '',
      'en' => '',
      'de' => '',
    ],
  ];

  /**
   * The mocked configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * UrlBuilderNextJs constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Build a NextJS/React compliant Cardis URL.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to translate into a Next URL.
   * @param \Symfony\Component\HttpFoundation\Request|null $request
   *   The incoming request.
   * @param string|null $langcode
   *   An optional langcode to generate the link in a specific language.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   *
   * @return string
   *   The Next URL.
   *
   * @psalm-suppress TypeDoesNotContainType
   */
  public function buildUrl(ContentEntityInterface $entity, ?Request $request = NULL, ?string $langcode = NULL): string {
    $bundle = $entity->bundle();

    if ($langcode === NULL) {
      $langcode = $entity->language()->getId();
    }

    // To prevent a missing key error, we prevent the rest.
    // If the bundle type is not specified.
    if (!\array_key_exists($bundle, self::NEXTJS_URLS_PREFIX)) {
      throw new \InvalidArgumentException(\sprintf('Bundle type [%s] is not present in the URLS prefixes', $bundle));
    }

    // To prevent a missing key error, throw an error if lang is not found.
    if (!\array_key_exists($langcode, self::NEXTJS_URLS_PREFIX[$bundle])) {
      throw new \InvalidArgumentException(\sprintf('Langcode %s is not valid for %s.', $langcode, $bundle));
    }

    if (!$entity->hasTranslation($langcode)) {
      throw new \InvalidArgumentException(\sprintf('Node is not translated in %s.', $langcode));
    }

    $translation = $entity->getTranslation($langcode);

    /** @var string $slug */
    $slug = $translation->toUrl('canonical')->toString();
    // Remove the langcode part as will be added manually later in the process.
    $slug = str_replace("/{$langcode}/", '/', $slug);

    // Build the complete destination path starting with the base URL.
    $base_url = $this->configFactory->get('frontend')->get('base_url');
    $dest = [$base_url];

    // Add the language prefix /fr|/de|/it only when necessary.
    if ($langcode !== 'en') {
      $dest[] = $langcode;
    }

    // Finalize the URL building with translated prefix for Next routing.
    if (!empty(self::NEXTJS_URLS_PREFIX[$bundle][$langcode])) {
      $dest[] = self::NEXTJS_URLS_PREFIX[$bundle][$langcode];
    }

    $dest[] = trim($slug, '/');
    $http_params = ($request && $request->query->count() > 0) ? '?' . http_build_query($request->query->all()) : NULL;

    return implode('/', $dest) . $http_params;
  }

}
