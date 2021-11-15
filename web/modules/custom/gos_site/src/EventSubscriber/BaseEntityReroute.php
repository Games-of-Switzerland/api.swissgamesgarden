<?php

namespace Drupal\gos_site\EventSubscriber;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\CacheableSecuredRedirectResponse;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\gos_site\UrlBuilderNextJs;
use Symfony\Component\HttpFoundation\Request;
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
   * The NextJs URL Builder.
   *
   * @var \Drupal\gos_site\UrlBuilderNextJs
   */
  protected $urlBuilderNextJs;

  /**
   * Constructs a new BaseEntityReroute object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\gos_site\UrlBuilderNextJs $url_builder_nextjs
   *   The NextJs URL Builder.
   */
  public function __construct(RouteMatchInterface $route_match, LanguageManagerInterface $language_manager, UrlBuilderNextJs $url_builder_nextjs) {
    $this->routeMatch = $route_match;
    $this->currentLang = $language_manager->getCurrentLanguage();
    $this->urlBuilderNextJs = $url_builder_nextjs;
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

  /**
   * Get Redirect Response to redirect current Node entity to the NextJS App.
   *
   * @param string $bundle
   *   The node bundle.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The incoming request.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   *
   * @return \Drupal\Core\Routing\CacheableSecuredRedirectResponse|null
   *   The Redirect Response. Null when no redirect rule found.
   */
  protected function redirectFromNode(string $bundle, Request $request): ?CacheableSecuredRedirectResponse {
    /** @var \Drupal\Core\Entity\ContentEntityBase $node */
    $node = $this->routeMatch->getParameter('node');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.node.canonical' && $node->bundle() === $bundle) {
      $langcode = $this->currentLang->getId();
      $dest = $this->urlBuilderNextJs->buildUrl($node, $request, $langcode);

      return $this->buildRedirection($dest, $node);
    }

    return NULL;
  }

}
