<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple studios on the same line into
 * multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_studios",
 *     source_module="gos_migrate"
 * )
 */
class Studios extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const ROW_EXPLODED_KEY = 'studio';

  /**
   * {@inheritdoc}
   */
  protected const ROW_ID_KEY = 'studio';

  /**
   * {@inheritdoc}
   */
  protected const SOURCE_KEY = 'studios';

}
