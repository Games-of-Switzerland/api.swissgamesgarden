<?php

namespace Drupal\gos_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts a single twitter link or @handle into an compliant twitter link.
 *
 * Examples:
 *
 * @code
 * process:
 *   field_twitter:
 *     plugin: gos_twitter_handle
 *     source: twitter_link_or_handle
 * @endcode
 *
 * If the source value was
 * '@playkids_ch', the transformed value would be
 * 'https://www.twitter.com/playkids_ch'.
 * Therefore, https://twitter.com/asylamba will stay the same.
 *
 * @MigrateProcessPlugin(
 *     id="gos_twitter_handle"
 * )
 */
class TwitterHandle extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value) && $value !== '0' && $value !== 0) {
      throw new MigrateSkipProcessException('The store link name should not be empty.');
    }

    if (filter_var($value, \FILTER_VALIDATE_URL)) {
      return $value;
    }

    if (mb_substr($value, 0, 1) === '@') {
      return 'https://www.twitter.com/' . mb_substr($value, 1, -1);
    }

    throw new MigrateException(sprintf('Twitter formatting was not possible on "%s".', $value));
  }

}
