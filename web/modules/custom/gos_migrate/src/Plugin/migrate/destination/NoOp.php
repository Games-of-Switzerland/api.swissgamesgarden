<?php

namespace Drupal\gos_migrate\Plugin\migrate\destination;

use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Row;

/**
 * Provides noop destination plugin.
 *
 * Sometimes you want to use the migrate system for more, or less, that it was
 * designed for and this means not having anything to do in the destination
 * plugin.  e.g. your migration is too far in to back out and you need to 'fix'
 * something. A quick lightweight migration where the work happens in the
 * process section could do this.
 *
 * This destination plugin is a 'quickfix' to the `null` destination plugin
 * always throwing false for `requirements_met`. I think this also sounds more
 * 'correct' in describing what the plugin does.
 *
 * @MigrateDestination(
 *     id="noop"
 * )
 */
class NoOp extends DestinationBase {

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress MissingParamType
   */
  public function __construct(array $configuration, string $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->supportsRollback = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(?MigrationInterface $migration = NULL): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []): bool {
    // The no-op always succeeds. Returning TRUE here prevents a 'failed'
    // being thrown. However, it also gives no indication of progress.
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function saveIdMapping(Row $row, array $destination_id_values, int $source_row_status = MigrateIdMapInterface::STATUS_IMPORTED, int $rollback_action = MigrateIdMapInterface::ROLLBACK_DELETE): void {
    // Do nothing.
  }

}
