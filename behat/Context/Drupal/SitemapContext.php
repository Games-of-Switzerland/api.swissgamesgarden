<?php

namespace Drupal\Behat\Context\Drupal;

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines Sitemap features from the specific context.
 */
class SitemapContext extends RawDrupalContext {

  /**
   * Generate sitemap.
   *
   * @BeforeScenario @sitemap
   */
  public function generateSitemap() {
    exec("../vendor/bin/drush simple-sitemap:generate");
  }

}
