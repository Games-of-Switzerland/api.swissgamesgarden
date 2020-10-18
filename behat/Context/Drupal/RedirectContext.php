<?php

namespace Drupal\Behat\Context\Drupal;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use PHPUnit\Framework\Assert as PHPUnit_Framework_Assert;

/**
 * Defines redirection features from the specific context.
 */
class RedirectContext extends RawDrupalContext {

  /**
   * Disable the redirection.
   *
   * @BeforeScenario @redirect_disable
   */
  public function unfollowRedirects($event): void {
    $this->getSession()->getDriver()->getClient()->followRedirects(FALSE);
  }

  /**
   * Enable the redirection.
   *
   * @AfterScenario @redirect_disable
   */
  public function followRedirects($event): void {
    $this->getSession()->getDriver()->getClient()->followRedirects(TRUE);
  }

  /**
   * Check Location header assert our expectation.
   *
   * @param string $destination
   *   The url destination to be redirect to.
   *
   * @Then /^I (?:am|should be) redirected to "([^"]*)"$/
   */
  public function iAmRedirectedTo(string $destination): void {
    $headers = $this->getSession()->getResponseHeaders();
    PHPUnit_Framework_Assert::assertTrue(isset($headers['Location'][0]));

    $redirectComponents = $headers['Location'][0];
    PHPUnit_Framework_Assert::assertEquals($destination, $redirectComponents);
  }

}
