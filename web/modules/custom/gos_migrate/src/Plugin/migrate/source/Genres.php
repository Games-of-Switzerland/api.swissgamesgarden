<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple genres on the same line
 * into multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_genres",
 *     source_module="gos_migrate"
 * )
 */
class Genres extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const ROW_EXPLODED_KEY = 'genre';

  /**
   * {@inheritdoc}
   */
  protected const ROW_ID_KEY = 'genre';

  /**
   * {@inheritdoc}
   */
  protected const SOURCE_KEY = 'genres';

}
