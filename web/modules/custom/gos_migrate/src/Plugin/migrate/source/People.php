<?php

namespace Drupal\gos_migrate\Plugin\migrate\source;

/**
 * {@inheritdoc}
 *
 * Extend the CSV migration tool to split multiple people on the same line into
 * multiple rows for Drupal migration system.
 *
 * @MigrateSource(
 *     id="csv_people",
 *     source_module="gos_migrate"
 * )
 */
class People extends BaseRowExploded {

  /**
   * {@inheritdoc}
   */
  protected const string ROW_EXPLODED_KEY = 'person';

  /**
   * {@inheritdoc}
   */
  protected const string ROW_ID_KEY = 'person';

  /**
   * {@inheritdoc}
   */
  protected const string SOURCE_KEY = 'persons';

}
