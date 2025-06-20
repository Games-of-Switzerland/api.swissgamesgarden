<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple sponsors on the same line
 * into multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_sponsors",
 *     source_module="gos_migrate"
 * )
 */
class Sponsors extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const string ROW_EXPLODED_KEY = 'sponsor';

  /**
   * {@inheritdoc}
   */
  protected const string ROW_ID_KEY = 'sponsor';

  /**
   * {@inheritdoc}
   */
  protected const string SOURCE_KEY = 'sponsors';

}
