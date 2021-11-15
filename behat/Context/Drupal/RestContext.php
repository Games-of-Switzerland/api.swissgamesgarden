<?php

namespace Drupal\Behat\Context\Drupal;

use Imbo\BehatApiExtension\Context\ApiContext;

/**
 * Defines REST features from the specific context.
 */
class RestContext extends ApiContext {

  /**
   * Set a CSRF Token for the next request.
   *
   * @Then I add the CSRF Token request header
   */
  public function setCsrfToken() {
    $request = $this->client->request('GET', '/session/token?_format=json');
    $token = $request->getBody()->getContents();
    $this->request = $this->request->withHeader('x-csrf-token', $token);
  }

}
