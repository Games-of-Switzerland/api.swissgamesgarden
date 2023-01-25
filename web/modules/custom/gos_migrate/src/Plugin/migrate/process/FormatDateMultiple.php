<?php

namespace Drupal\gos_migrate\Plugin\migrate\process;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts date/datetime from many formats to another one standardized one.
 *
 * Available configuration keys
 * - from_formats: The many source formats string as accepted by.
 *
 *   @see http://php.net/manual/datetime.createfromformat.php \DateTime::createFromFormat. @endlink
 * - to_formats: The destination format.
 * - from_timezone: String identifying the required source time zone, see
 *   DateTimePlus::__construct().
 * - to_timezone: String identifying the required destination time zone, see
 *   DateTimePlus::__construct().
 * - on_error: (optional) What to do if the input value generate an error.
 * Possible values:
 *   - row: Skips the entire row when an error is raised.
 *   - process: Prevents further processing of the input property when an error
 *     is raised.
 *   - exception: Throw a standard MigrateException when any error is raised.
 * - settings: keyed array of settings, see DateTimePlus::__construct().
 *
 * Configuration keys from_timezone and to_timezone are both optional. Possible
 * input variants:
 * - Both from_timezone and to_timezone are empty. Date will not be converted
 *   and be treated as date in default timezone.
 * - Only from_timezone is set. Date will be converted from timezone specified
 *   in from_timezone key to the default timezone.
 * - Only to_timezone is set. Date will be converted from the default timezone
 *   to the timezone specified in to_timezone key.
 * - Both from_timezone and to_timezone are set. Date will be converted from
 *   timezone specified in from_timezone key to the timezone specified in
 *   to_timezone key.
 *
 * Examples:
 *
 * @code
 * process:
 *   field_date:
 *     plugin: format_date_multiple
 *     from_formats:
 *       - 'Y'
 *       - 'Y-m-d'
 *     to_formats:
 *       - 'Y-01-01'
 *       - 'Y-m-d'
 *     source: event_date
 * @endcode
 *
 * If the source value was '1955/01/05/' or '1955' the transformed value would
 * be 1955-01-05 or '1955-01-01.
 *
 * @see \DateTime::createFromFormat()
 * @see \Drupal\Component\Datetime\DateTimePlus::__construct()
 * @see \Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *     id="format_date_multiple"
 * )
 */
class FormatDateMultiple extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress InvalidNullableReturnType
   * @psalm-suppress NullableReturnStatement
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value) && $value !== '0' && $value !== 0) {
      return '';
    }

    // Validate the configuration.
    if (empty($this->configuration['from_formats'])) {
      throw new MigrateException('Formats date plugin is missing from_formats configuration.');
    }

    if (empty($this->configuration['to_formats'])) {
      throw new MigrateException('Formats date plugin is missing to_formats configuration.');
    }

    $from_formats = (string) $this->configuration['from_formats'];
    $to_formats = (string) $this->configuration['to_formats'];
    $system_timezone = date_default_timezone_get();
    $default_timezone = !empty($system_timezone) ? $system_timezone : 'UTC';
    $from_timezone = $this->configuration['from_timezone'] ?? $default_timezone;
    $to_timezone = $this->configuration['to_timezone'] ?? $default_timezone;
    $settings = $this->configuration['settings'] ?? [];
    $on_error = $this->configuration['on_error'] ?? 'exception';

    // Attempts to transform the supplied date using the defined input format.
    // DateTimePlus::createFromFormat can throw exceptions, so we need to
    // explicitly check for problems.
    foreach ($from_formats as $index => $from_format) {
      try {
        return DateTimePlus::createFromFormat($from_format, $value, $from_timezone, $settings)->format($to_formats[$index], ['timezone' => $to_timezone]);
      }
      catch (\Exception $e) {
        continue;
      }
    }

    if ($on_error === 'row') {
      throw new MigrateSkipProcessException(sprintf("Format date plugin could not transform '%s' using any formats '%s' for destination '%s'.", $value, implode(',', $from_formats), $destination_property));
    }

    if ($on_error === 'process') {
      throw new MigrateSkipRowException(sprintf("Format date plugin could not transform '%s' using any formats '%s' for destination '%s'.", $value, implode(',', $from_formats), $destination_property));
    }

    if ($on_error === 'exception') {
      throw new MigrateException(sprintf("Format date plugin could not transform '%s' using any formats '%s' for destination '%s'.", $value, implode(',', $from_formats), $destination_property));
    }

    if ($on_error === 'nullable') {
      return '';
    }

    throw new MigrateException(sprintf("Format date plugin could not transform '%s' using any formats '%s' for destination '%s'.", $value, implode(',', $from_formats), $destination_property));
  }

}
