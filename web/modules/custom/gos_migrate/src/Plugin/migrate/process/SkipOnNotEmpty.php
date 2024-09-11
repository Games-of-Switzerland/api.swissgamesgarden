<?php

namespace Drupal\gos_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Skips the row or process if the given source value is set.
 *
 * Examples:
 *
 * @code
 * process:
 *   field_type_exists:
 *     plugin: skip_on_not_empty
 *     method: row
 *     source: field_skip
 *     message: 'Field field_skip is set'
 *
 * @endcode
 * If 'field_skip' is set, the entire row is skipped and the 'message' is logged
 * in the message table.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *     id="skip_on_not_empty"
 * )
 */
class SkipOnNotEmpty extends ProcessPluginBase {

  /**
   * Stops processing the current property when value is set.
   *
   * @param mixed $value
   *   The input value.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migration in which this process is being executed.
   * @param \Drupal\migrate\Row $row
   *   The row from the source to process.
   * @param string $destination_property
   *   The destination property currently worked on. This is only used together
   *   with the $row above.
   *
   * @return mixed
   *   The input value, $value, if it is not empty.
   */
  public function process($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($value) {
      $message = \array_key_exists('message', $this->configuration) ? $this->configuration['message'] : '';
      $migrate_executable->saveMessage($message);
      $this->stopPipeline();

      return NULL;
    }

    return $value;
  }

  /**
   * Skips the current row when value is set.
   *
   * @param mixed $value
   *   The input value.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migration in which this process is being executed.
   * @param \Drupal\migrate\Row $row
   *   The row from the source to process.
   * @param string $destination_property
   *   The destination property currently worked on. This is only used together
   *   with the $row above.
   *
   * @throws \Drupal\migrate\MigrateSkipRowException
   *   Thrown if the source property is not set and the row should be skipped,
   *   records with STATUS_IGNORED status in the map.
   *
   * @return mixed
   *   The input value, $value, if it is not empty.
   */
  public function row($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($value) {
      $message = \array_key_exists('message', $this->configuration) ? $this->configuration['message'] : '';

      throw new MigrateSkipRowException($message);
    }

    return $value;
  }

}
