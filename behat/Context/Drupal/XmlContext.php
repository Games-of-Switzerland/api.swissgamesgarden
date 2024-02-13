<?php

namespace Drupal\Behat\Context\Drupal;

use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines XML features from the specific context.
 */
class XmlContext extends RawMinkContext {

  /**
   * Checks that the specified XML element exists.
   *
   * @throws \Exception
   *
   * @Then the XML element :element should exist(s)
   */
  public function theXmlElementShouldExist(string $element): \DOMNodeList {
    $dom = $this->getDom();

    $xpath = new \DOMXpath($dom);

    $namespaces = $this->getNamespaces($dom);
    $this->registerNamespace($xpath, $namespaces);
    $element = $this->fixNamespace($element, $namespaces);

    $elements = $xpath->query($element);
    $elements = ($elements === FALSE) ? new \DOMNodeList() : $elements;

    if ($elements->length == 0) {
      throw new \Exception("The element '$element' does not exist.");
    }

    return $elements;
  }

  /**
   * Checks that the specified XML element does not exist.
   *
   * @Then the XML element :element should not exist(s)
   */
  public function theXmlElementShouldNotExist($element) {
    $this->not(function () use ($element) {
      $this->theXmlElementShouldExist($element);
    }, "The element '$element' exists.");
  }

  /**
   * Checks that the given XML element contains the given value.
   *
   * @Then the XML element :element should contain :text
   */
  public function theXmlElementShouldContain($element, $text): void {
    $elements = $this->theXmlElementShouldExist($element);

    $this->assertContains($text, $elements->item(0)->nodeValue);
  }

  /**
   * Init a DomDocument object for XML.
   */
  private function getDom(): \DOMDocument {
    $content = $this->getSession()->getPage()->getContent();

    $dom = new \DomDocument();
    $dom->strictErrorChecking = FALSE;
    $dom->validateOnParse = FALSE;
    $dom->preserveWhiteSpace = TRUE;
    $dom->loadXML($content, LIBXML_PARSEHUGE);
    return $dom;
  }

  /**
   * Register the rootNS prefix.
   */
  private function registerNamespace(\DOMXpath $xpath, array $namespaces): void {
    foreach ($namespaces as $prefix => $namespace) {
      if (empty($prefix)) {
        $prefix = 'rootns';
      }
      $xpath->registerNamespace($prefix, $namespace);
    }
  }

  /**
   * Fix queries to the default namespace if any namespaces are defined.
   */
  private function fixNamespace(string $element, array $namespaces): string {
    if (!empty($namespaces)) {
      for ($i = 0; $i < 2; ++$i) {
        $element = preg_replace('/\/(\w+)(\[[^]]+\])?\//', '/rootns:$1$2/', $element);
      }
      $element = preg_replace('/\/(\w+)(\[[^]]+\])?$/', '/rootns:$1$2', $element);
    }
    return $element;
  }

  /**
   * Extract Namepsace from givem DomDocument.
   */
  private function getNamespaces(\DomDocument $dom): array {
    $xml = simplexml_import_dom($dom);
    return $xml->getNamespaces(TRUE);
  }

  /**
   * Assert or throw an exception.
   */
  protected function assert(bool $test, string $message): void {
    if ($test === FALSE) {
      throw new ExpectationException($message, $this->getSession()->getDriver());
    }
  }

  /**
   * Assert a given callable throw an exception and silent it.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  protected function not(callable $callbable, string $errorMessage): void {
    try {
      $callbable();
    }
    catch (\Exception $e) {
      return;
    }

    throw new ExpectationException($errorMessage, $this->getSession()->getDriver());
  }

  /**
   * Assert a string is contained into another one.
   */
  protected function assertContains(string $expected, string $actual, string $message = NULL): void {
    $regex = '/' . preg_quote($expected, '/') . '/ui';

    $this->assert(
      preg_match($regex, $actual) > 0,
      $message ?: "The string '$expected' was not found."
    );
  }

}
