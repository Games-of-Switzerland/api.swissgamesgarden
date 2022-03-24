<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple cantons on the same line
 * into multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_cantons",
 *     source_module="gos_migrate"
 * )
 */
class Cantons extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const ROW_EXPLODED_KEY = 'canton';

  /**
   * {@inheritdoc}
   */
  protected const ROW_ID_KEY = 'canton';

  /**
   * {@inheritdoc}
   */
  protected const SOURCE_KEY = 'cantons';

}
