<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple publishers on the same line
 * into multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_publishers",
 *     source_module="gos_migrate"
 * )
 */
class Publishers extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const string ROW_EXPLODED_KEY = 'publisher';

  /**
   * {@inheritdoc}
   */
  protected const string ROW_ID_KEY = 'publisher';

  /**
   * {@inheritdoc}
   */
  protected const string SOURCE_KEY = 'publishers';

}
