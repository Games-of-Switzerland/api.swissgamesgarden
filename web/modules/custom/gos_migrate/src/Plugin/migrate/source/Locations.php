<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple locations on the same line
 * into multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_locations",
 *     source_module="gos_migrate"
 * )
 */
class Locations extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const ROW_EXPLODED_KEY = 'location';

  /**
   * {@inheritdoc}
   */
  protected const ROW_ID_KEY = 'location';

  /**
   * {@inheritdoc}
   */
  protected const SOURCE_KEY = 'locations';

}
