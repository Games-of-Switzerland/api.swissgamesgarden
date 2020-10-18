<?php

namespace Drupal\gos_site\EventSubscriber;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\CacheableSecuredRedirectResponse;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class to Redirect Drupal canonical entity to Next app.
 */
abstract class BaseEntityReroute {

  /**
   * The current language.
   *
   * @var \Drupal\Core\Language\LanguageInterface
   */
  protected $currentLang;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new BaseEntityReroute object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(RouteMatchInterface $route_match, LanguageManagerInterface $language_manager) {
    $this->routeMatch = $route_match;
    $this->currentLang = $language_manager->getCurrentLanguage();
  }

  /**
   * Build a cacheable redirection.
   *
   * @param string $dest
   *   The destination Next URL.
   * @param \Drupal\Core\Entity\ContentEntityBase $entity
   *   The entity to redirect.
   *
   * @return \Drupal\Core\Routing\CacheableSecuredRedirectResponse
   *   The response with cacheability destination.
   */
  protected function buildRedirection(string $dest, ContentEntityBase $entity): CacheableSecuredRedirectResponse {
    $response = new TrustedRedirectResponse($dest);
    $cache = new CacheableMetadata();

    $cache->addCacheableDependency($entity);
    $cache->addCacheContexts(['url.path', 'url.query_args']);

    $response->addCacheableDependency($cache);
    $response->setStatusCode(Response::HTTP_MOVED_PERMANENTLY);

    return $response;
  }

}
