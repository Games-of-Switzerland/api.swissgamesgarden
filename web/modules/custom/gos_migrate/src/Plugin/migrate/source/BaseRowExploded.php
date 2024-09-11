<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * Split multiple values (on the same row) into multiple rows.
 *
 * By having multiple rows with uniq identifier, the Drupal migration system is
 * then able to rollback migrations properly.
 */
abstract class BaseRowExploded extends CSV {

  /**
   * The key of exploded row value.
   *
   * @var string
   */
  protected const ROW_EXPLODED_KEY = '';

  /**
   * The new prepared key that will be uniq by exploded row.
   *
   * @var string
   */
  protected const ROW_ID_KEY = '';

  /**
   * The Source key that may contains multiple values separated by comma.
   *
   * @var string
   */
  protected const SOURCE_KEY = '';

  /**
   * {@inheritdoc}
   *
   * Split single row with values separated by a comma into multiple rows.
   */
  protected function getGenerator(\Iterator $records): ?\Generator {
    foreach ($records as $record) {
      $record[$this::ROW_ID_KEY] = trim($record[$this::SOURCE_KEY]);

      /** @var array|false $items */
      $items = explode(',', $record[$this::SOURCE_KEY]);

      if ($items !== FALSE) {
        // Remove empty items.
        $items = array_filter(array_map('trim', $items));

        foreach ($items as $item) {
          $record[$this::ROW_ID_KEY] = trim($item);
          $record[$this::ROW_EXPLODED_KEY] = trim($item);

          yield $record;
        }

        continue;
      }

      yield $record;
    }
  }

}
