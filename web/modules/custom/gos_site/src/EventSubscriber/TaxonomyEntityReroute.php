<?php

namespace Drupal\gos_site\EventSubscriber;

use Drupal\Core\Routing\CacheableSecuredRedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirect Drupal Taxonomy canonical to pages in Next app.
 */
class TaxonomyEntityReroute extends BaseEntityReroute implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [
        ['isGenre'],
        ['isPlatform'],
        ['isLanguage'],
        ['isLocation'],
        ['isCanton'],
        ['isPublisher'],
        ['isSponsor'],
      ],
    ];
  }

  /**
   * Redirect Canton canonical to no-where.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  public function isCanton(RequestEvent $event): void {
    /** @var \Drupal\Core\Entity\ContentEntityBase $taxonomy_term */
    $taxonomy_term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.taxonomy_term.canonical' && $taxonomy_term->bundle() === 'canton') {
      $response = $this->shutdownCanonicalTaxonomyTermAccess($event);
      $event->setResponse($response);
    }
  }

  /**
   * Redirect Genre canonical to no-where.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  public function isGenre(RequestEvent $event): void {
    /** @var \Drupal\Core\Entity\ContentEntityBase $taxonomy_term */
    $taxonomy_term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.taxonomy_term.canonical' && $taxonomy_term->bundle() === 'genre') {
      $response = $this->shutdownCanonicalTaxonomyTermAccess($event);
      $event->setResponse($response);
    }
  }

  /**
   * Redirect Language canonical to no-where.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  public function isLanguage(RequestEvent $event): void {
    /** @var \Drupal\Core\Entity\ContentEntityBase $taxonomy_term */
    $taxonomy_term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.taxonomy_term.canonical' && $taxonomy_term->bundle() === 'language') {
      $response = $this->shutdownCanonicalTaxonomyTermAccess($event);
      $event->setResponse($response);
    }
  }

  /**
   * Redirect Location canonical to no-where.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  public function isLocation(RequestEvent $event): void {
    /** @var \Drupal\Core\Entity\ContentEntityBase $taxonomy_term */
    $taxonomy_term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.taxonomy_term.canonical' && $taxonomy_term->bundle() === 'location') {
      $response = $this->shutdownCanonicalTaxonomyTermAccess($event);
      $event->setResponse($response);
    }
  }

  /**
   * Redirect Platform canonical to no-where.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  public function isPlatform(RequestEvent $event): void {
    /** @var \Drupal\Core\Entity\ContentEntityBase $taxonomy_term */
    $taxonomy_term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.taxonomy_term.canonical' && $taxonomy_term->bundle() === 'platform') {
      $response = $this->shutdownCanonicalTaxonomyTermAccess($event);
      $event->setResponse($response);
    }
  }

  /**
   * Redirect Publisher canonical to no-where.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  public function isPublisher(RequestEvent $event): void {
    /** @var \Drupal\Core\Entity\ContentEntityBase $taxonomy_term */
    $taxonomy_term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.taxonomy_term.canonical' && $taxonomy_term->bundle() === 'publisher') {
      $response = $this->shutdownCanonicalTaxonomyTermAccess($event);
      $event->setResponse($response);
    }
  }

  /**
   * Redirect Sponsor canonical to no-where.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  public function isSponsor(RequestEvent $event): void {
    /** @var \Drupal\Core\Entity\ContentEntityBase $taxonomy_term */
    $taxonomy_term = $this->routeMatch->getParameter('taxonomy_term');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.taxonomy_term.canonical' && $taxonomy_term->bundle() === 'sponsor') {
      $response = $this->shutdownCanonicalTaxonomyTermAccess($event);
      $event->setResponse($response);
    }
  }

  /**
   * Shutdown Taxonomy Canonical Access by redirecting to Frontpage.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   A response for a request.
   *
   * @return \Drupal\Core\Routing\CacheableSecuredRedirectResponse
   *   The response with cacheability destination.
   *
   * @psalm-suppress PossiblyInvalidArgument
   */
  private function shutdownCanonicalTaxonomyTermAccess(RequestEvent $event): CacheableSecuredRedirectResponse {
    /** @var \Drupal\Core\Entity\ContentEntityBase $taxonomy_term */
    $taxonomy_term = $this->routeMatch->getParameter('taxonomy_term');
    $dest = Url::fromRoute('<front>')->toString();

    return $this->buildRedirection($dest, $taxonomy_term);
  }

}
