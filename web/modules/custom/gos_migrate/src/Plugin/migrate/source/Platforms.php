<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple platforms on the same line
 * into multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_platforms",
 *     source_module="gos_migrate"
 * )
 */
class Platforms extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const string ROW_EXPLODED_KEY = 'platform';

  /**
   * {@inheritdoc}
   */
  protected const string ROW_ID_KEY = 'platform';

  /**
   * {@inheritdoc}
   */
  protected const string SOURCE_KEY = 'platforms';

}
