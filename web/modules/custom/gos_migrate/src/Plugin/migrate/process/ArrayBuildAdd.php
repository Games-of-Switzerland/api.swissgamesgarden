<?php

namespace Drupal\gos_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Add new key/value pair to an existing array based on key and value config.
 *
 * The array_build_add plugin allow you to push a single associative key/value
 * pair for each array in the input value, which is expected to be an
 * array of arrays. The keys of the returned array will be determined by the
 * 'key' configuration option, and the value will be determined by the
 * 'source_value' option. The 'source_value' should be a source property or
 * a destination property.
 *
 * Available configuration keys
 *   - source: The Source property to alter.
 *   - source_value: Source property value associated to the new key.
 *   - key: The key to be added to the source arrays.
 *
 * Example:
 *
 * Consider the migration of release by platforms.
 * The source is an array of all the platforms with a single release source:
 *
 * @code
 * release: date
 *   2020-05-22
 * ...
 * platforms: Array
 * (
 *   [0] => Array
 *     (
 *       [value] => Windows
 *     )
 *   [1] => Array
 *     (
 *       [value] => macOS
 *     )
 * ...
 *
 * @endcode
 *
 * The destination should be an array of all the platforms with a release date:
 *
 * @code
 * platforms: Array
 * (
 *   [0] => Array
 *     (
 *       [value] => Windows
 *       [date] => 2020-05-22
 *     )
 *   [1] => Array
 *     (
 *       [value] => macOS
 *       [date] => 2020-05-22
 *     )
 * ...
 *
 * @endcode
 *
 * @code
 * source:
 *   my_date:
 *     - release_date
 *   my_flat_array:
 *     - category1
 *     - category2
 * process:
 *   my_new_array:
 *     plugin: array_build_add
 *     source: my_flat_array
 *     key: 'date'
 *     source_value: my_date
 *
 * @endcode
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *     id="array_build_add",
 *     handle_multiples=TRUE
 * )
 */
class ArrayBuildAdd extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    $new_value = $value;

    if (!is_iterable($value)) {
      throw new MigrateException('The source should be an iterable.');
    }

    $keyname = (isset($this->configuration['key']) && \is_string($this->configuration['key']) && $this->configuration['key'] !== '') ? $this->configuration['key'] : 'keyname';

    // Check if we have to get the property value from the source or a
    // processed destination property (starting with @).
    $source_value = $row->getSourceProperty($this->configuration['source_value']);

    if ($this->configuration['source_value'][0] === '@') {
      $source_value = $row->getDestinationProperty(ltrim($this->configuration['source_value'], '@'));
    }

    if ($source_value === NULL || empty($source_value)) {
      return $new_value;
    }

    foreach ((array) $value as $key => $current_value) {
      // Checks that $current_value is an array.
      if (!\is_array($current_value)) {
        throw new MigrateException('The input should be an array of arrays.');
      }

      // Checks that the key exists.
      if (\array_key_exists($keyname, $current_value)) {
        throw new MigrateException("The key '" . $keyname . "' already exist.");
      }

      $new_value[$key][$keyname] = $source_value;
    }

    return $new_value;
  }

}
