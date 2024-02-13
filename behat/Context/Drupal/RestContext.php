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

  /**
   * Request a path as binary file upload.
   *
   * @param string $path
   *   The path to request.
   *
   * @When I POST binary file on :path
   */
  public function requestPathBinaryFile($path) {
    $this->setRequestPath($path);
    $this->setRequestMethod('POST');

    if (!$this->requestOptions['multipart'] && $this->requestOptions['multipart'][0]['name'] === 'file') {
      throw new \RuntimeException('The request does not have an attached file.');
    }

    // Read the Imbo multipart file and set it as the request body.
    $file = $this->requestOptions['multipart'][0];
    unset($this->requestOptions['multipart']);
    $this->setRequestBody($file['contents']);

    return $this->sendRequest();
  }

}
