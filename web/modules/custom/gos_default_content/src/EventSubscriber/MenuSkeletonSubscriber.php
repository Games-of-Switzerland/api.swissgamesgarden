<?php

namespace Drupal\gos_default_content\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\default_content\Event\DefaultContentEvents;
use Drupal\default_content\Event\ImportEvent;
use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * Generate the default menu(s) for Games of Switzerland to works properly.
 */
class MenuSkeletonSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[DefaultContentEvents::IMPORT][] = ['setupMainMenu', 1000];
    return $events;
  }

  /**
   * Setup the default main swiss menu.
   *
   * @param \Drupal\default_content\Event\ImportEvent $event
   *   The Import event.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function setupMainMenu(ImportEvent $event) {
    $items = [
      [
        'title' => 'Contact',
        'link' => ['uri' => 'internal:#'],
      ],
      [
        'title' => 'About',
        'link' => ['uri' => 'internal:#'],
      ],
      [
        'title' => 'Games',
        'link' => ['uri' => 'internal:#'],
      ],
    ];

    foreach ($items as $item) {
      $menu_link = MenuLinkContent::create([
        'title' => $item['title'],
        'link' => $item['link'],
        'menu_name' => 'main',
        'expanded' => TRUE,
      ]);
      $menu_link->save();
    }
  }

}
