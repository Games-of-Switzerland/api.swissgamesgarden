<?php

namespace Drupal\gos_default_content\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\default_content\Event\DefaultContentEvents;
use Drupal\default_content\Event\ImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Generate, update & alter the default games for Games of Switzerland.
 */
final readonly class GamesSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a new AgentsSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    /**
     * The entity type manager.
     */
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Alter agents with Avatar generated images.
   *
   * @param \Drupal\default_content\Event\ImportEvent $event
   *   The Import event.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function generateImages(ImportEvent $event): void {
    /** @var \Drupal\node\NodeStorageInterface $node_storage */
    $node_storage = $this->entityTypeManager->getStorage('node');

    $games = $node_storage->loadByProperties(['type' => 'game']);

    foreach ($games as $game) {
      $game->field_images->generateSampleItems();
      $game->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      DefaultContentEvents::IMPORT => [
        ['generateImages', 1000],
      ],
    ];
  }

}
