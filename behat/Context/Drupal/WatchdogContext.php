<?php

namespace Drupal\Behat\Context\Drupal;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Exception;

/**
 * Defines Watchdog features from the specific context.
 */
class WatchdogContext extends RawDrupalContext {

  /**
   * Cleanup the watchdog table.
   *
   * Clear out anything that might be in the watchdog table from god knows
   * where.
   *
   * @BeforeScenario
   */
  public static function cleanupWatchdog() {
    $connection = \Drupal::service('database');
    $connection->truncate('watchdog')->execute();
  }

  /**
   * Search in the watchdog table if any error has been raised.
   *
   * @AfterStep
   */
  public function detectWatchdog() {
    $connection = \Drupal::service('database');

    $logs = $connection->select('watchdog')
      ->fields('watchdog', ['wid', 'message', 'variables'])
      ->condition('type', 'php')
      ->execute()
      ->fetchAll();

    if (empty($logs)) {
      return;
    }

    foreach ($logs as $log) {
      // Make the substitutions easier to read in the log.
      $log->variables = unserialize($log->variables);
      print_r($log);
    }

    throw new Exception('PHP errors logged to watchdog in this step.');
  }

}
