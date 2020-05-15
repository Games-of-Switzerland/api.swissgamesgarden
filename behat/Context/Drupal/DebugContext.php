<?php

namespace Drupal\Behat\Context\Drupal;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Drupal\Component\Utility\Random;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Exception;

/**
 * Defines Debug features from the specific context.
 */
class DebugContext extends RawDrupalContext {

  /**
   * The log path directory from the root.
   *
   * @var string
   */
  public $logPath;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct($log_path) {
    // Create the directory if not already exists.
    if (!is_dir($log_path)) {
      mkdir($log_path, 0777, TRUE);
    }

    $this->logPath = $log_path;
  }

  /**
   * Save the HTML of the current page.
   *
   * @Then Save me the HTML and show it
   */
  public function saveHtml() {
    $file_and_path = $this->logTo($this->getSession()->getDriver()->getContent(), 'html', 'html');

    if (\PHP_OS === 'Darwin' && \PHP_SAPI === 'cli') {
      exec('open -a "Google Chrome.app" ' . $file_and_path);
    }
  }

  /**
   * Save the HTML - All Driver - when a Step fail.
   *
   * @AfterStep
   */
  public function saveHtmlAfterFailedStep(afterStepScope $scope) {
    if ($scope->getTestResult()->getResultCode() !== 99) {
      return;
    }

    $file_and_path = $this->logTo($this->getSession()->getDriver()->getContent(), 'fail', 'html');

    if (\PHP_OS === 'Darwin' && \PHP_SAPI === 'cli') {
      // exec('open -a "Safari.app" ' . $file_and_path);.
      exec('open -a "Google Chrome.app" ' . $file_and_path);
    }
  }

  /**
   * Take a screenshot of the current page.
   *
   * @Then Take a screenshot
   */
  public function takeScreenshot() {
    $driver = $this->getSession()->getDriver();

    if (!$driver instanceof Selenium2Driver) {
      return;
    }

    $file_and_path = $this->logTo($this->getSession()->getDriver()->getScreenshot(), 'screenshot', 'png');

    if (\PHP_OS === 'Darwin' && \PHP_SAPI === 'cli') {
      // exec('open -a "Safari.app" ' . $file_and_path);.
      exec('open -a "Google Chrome.app" ' . $file_and_path);
    }
  }

  /**
   * Take a Screenshot - Selenium only - when a Step fail.
   *
   * @AfterStep
   */
  public function takeScreenShotAfterFailedStep(afterStepScope $scope) {
    if ($scope->getTestResult()->getResultCode() !== 99) {
      return;
    }

    $driver = $this->getSession()->getDriver();

    if (!$driver instanceof Selenium2Driver) {
      return;
    }

    $file_and_path = $this->logTo($this->getSession()->getDriver()->getScreenshot(), 'fail', 'png');

    if (\PHP_OS === 'Darwin' && \PHP_SAPI === 'cli') {
      // exec('open -a "Safari.app" ' . $file_and_path);.
      exec('open -a "Google Chrome.app" ' . $file_and_path);
    }
  }

  /**
   * Log the given content.
   *
   * @param string $content
   *   The content to log into a random file.
   * @param string $prefix
   *   The file prefix to use.
   * @param string $extension
   *   The extension to use.
   *
   * @throws \Exception
   *
   * @return string
   *   The file path of logged file.
   */
  private function logTo($content, $prefix, $extension) {
    if (!is_dir($this->logPath)) {
      throw new Exception(sprintf('The log directory "%s" does not exists.', $this->logPath));
    }

    $random = new Random();
    $file_and_path = $this->logPath . \DIRECTORY_SEPARATOR . $prefix . '_' . $random->name(10, TRUE) . '.' . $extension;
    file_put_contents($file_and_path, $content);

    print sprintf("\e[0;34mLog file: %s\e[0m\n", $file_and_path);

    return $file_and_path;
  }

}
