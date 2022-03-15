<?php

namespace Drupal\Behat\Context\Drupal;

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines Watchdog features from the specific context.
 */
class WatchdogContext extends RawDrupalContext {

  /**
   * Does Watchdog report must be ignored.
   *
   * @var bool
   */
  protected bool $watchdogIgnore = FALSE;

  /**
   * Cleanup the watchdog table.
   *
   * Clear out anything that might be in the watchdog table from god knows
   * where.
   *
   * @BeforeScenario
   */
  public function cleanupWatchdog(): void {
    $connection = \Drupal::service('database');
    $connection->truncate('watchdog')->execute();
  }

  /**
   * Ignore watchdog report for the current scenario.
   *
   * @BeforeScenario @watchdog-ignore
   */
  public function ignoreWatchdog(): void {
    $this->watchdogIgnore = TRUE;
  }

  /**
   * Search in the watchdog table if any error has been raised.
   *
   * @AfterStep
   */
  public function detectWatchdog(): void {
    if ($this->watchdogIgnore) {
      return;
    }

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
      $log->variables = unserialize($log->variables, ['allowed_classes' => FALSE]);
      print_r($log);
    }

    throw new \Exception('PHP errors logged to watchdog in this step.');
  }

}
