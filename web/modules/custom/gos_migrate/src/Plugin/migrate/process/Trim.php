<?php

namespace Drupal\gos_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Transforms a value by stripping whitespaces (or other) on each string side.
 *
 * Available configuration keys:
 * - mask: Simply list all characters that you want to be stripped.
 *         With .. you can specify a range of characters.
 *
 * Example:
 *
 * @code
 * process:
 *   field_string:
 *     plugin: trim
 *     source: my_string_value
 * @endcode
 *
 * @code
 * process:
 *   field_string:
 *     plugin: trim
 *     source: my_string_value
 *     mask: "\x00..\x1F"
 * @endcode
 * Trim the ASCII control characters at the beginning and end of my_string_value
 * (from 0 to 31 inclusive)
 *
 * @MigrateProcessPlugin(
 *     id="trim"
 * )
 */
class Trim extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!empty($this->configuration['mask'])) {
      return trim($value, $this->configuration['mask']);
    }

    return trim($value);
  }

}
