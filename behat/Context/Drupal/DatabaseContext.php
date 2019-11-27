<?php

namespace Drupal\Behat\Context\Drupal;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\Component\Utility\Random;

/**
 * Defines Database features from the specific context.
 */
class DatabaseContext extends RawDrupalContext implements SnippetAcceptingContext {

  protected static $dump = NULL;

  /**
   * The dump path directory from the root.
   *
   * @var string
   */
  public $dumpPath;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct($dump_path) {
    // Create the directory if not already exists.
    if (!is_dir($dump_path)) {
      mkdir($dump_path, 0777, TRUE);
    }

    $this->dumpPath = $dump_path;
  }

  /**
   * Generate a dump of the currente database state.
   *
   * @BeforeScenario @preserveDatabase
   */
  public function preserveDatabase() {
    $random = new Random();
    self::$dump = $this->dumpTo($random->name(10, TRUE));
  }

  /**
   * Reload a dump of the previous database state.
   *
   * @AfterScenario @preserveDatabase
   */
  public function reloadDatabase() {
    $this->loadFrom(self::$dump);
  }

  /**
   * Dump the current database into the given filename.
   *
   * @param string $filename
   *   The filename.
   *
   * @return string
   *   The dump file path.
   */
  private function dumpTo($filename) {
    if (!is_dir($this->dumpPath)) {
      throw new \Exception(sprintf('The dump directory "%s" does not exists.', $this->dumpPath));
    }

    $file_and_path = $this->dumpPath . DIRECTORY_SEPARATOR . $filename . '.sql';

    echo sprintf("\e[0;34mSQL dump: %s\e[0m\n", $file_and_path);
    exec("../vendor/bin/drush sql-dump --result-file=$file_and_path -y");

    return $file_and_path;
  }

  /**
   * Reload the database file.
   *
   * @param string $file_and_path
   *   The filename & path.
   */
  private function loadFrom($file_and_path) {
    if (!is_file($file_and_path)) {
      throw new \Exception(sprintf('The dump file "%s" does not exists.', $file_and_path));
    }

    exec("../vendor/bin/drush sql-cli < $file_and_path");
    unlink($file_and_path);
  }

}
