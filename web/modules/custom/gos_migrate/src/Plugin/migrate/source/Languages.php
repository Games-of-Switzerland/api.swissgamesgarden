<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple languages on the same line
 * into multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_languages",
 *     source_module="gos_migrate"
 * )
 */
class Languages extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const ROW_EXPLODED_KEY = 'language';

  /**
   * {@inheritdoc}
   */
  protected const ROW_ID_KEY = 'language';

  /**
   * {@inheritdoc}
   */
  protected const SOURCE_KEY = 'languages';

}
