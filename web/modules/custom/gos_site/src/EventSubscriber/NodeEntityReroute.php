<?php

namespace Drupal\gos_site\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirect Drupal canonical to pages in Next app.
 */
class NodeEntityReroute extends BaseEntityReroute implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => [
        ['isPage'],
        ['isGame'],
        ['isPeople'],
        ['isStudio'],
      ],
    ];
  }

  /**
   * Redirect Game canonical to Next/React page.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   A response for a request.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function isGame(GetResponseEvent $event): void {
    $request = $event->getRequest();
    $response = $this->redirectFromNode('game', $request);

    if ($response) {
      $event->setResponse($response);
    }
  }

  /**
   * Redirect Page canonical to no-where.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   A response for a request.
   */
  public function isPage(GetResponseEvent $event): void {
    /** @var \Drupal\Core\Entity\ContentEntityBase $node */
    $node = $this->routeMatch->getParameter('node');
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'entity.node.canonical' && $node->bundle() === 'page') {
      /** @var string $dest */
      $dest = Url::fromRoute('<front>')->toString();
      $response = $this->buildRedirection($dest, $node);
      $event->setResponse($response);
    }
  }

  /**
   * Redirect People canonical to Next/React page.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   A response for a request.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function isPeople(GetResponseEvent $event): void {
    $request = $event->getRequest();
    $response = $this->redirectFromNode('people', $request);

    if ($response) {
      $event->setResponse($response);
    }
  }

  /**
   * Redirect Studio canonical to Next/React page.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   A response for a request.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function isStudio(GetResponseEvent $event): void {
    $request = $event->getRequest();
    $response = $this->redirectFromNode('studio', $request);

    if ($response) {
      $event->setResponse($response);
    }
  }

}
