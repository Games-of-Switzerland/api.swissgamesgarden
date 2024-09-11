<?php

namespace Drupal\gos_rest\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides methods to build a cacheable Resource.
 */
trait ResourceCacheableResponseTrait {

  /**
   * The Resource response that will be returned.
   *
   * @var \Drupal\Core\Cache\CacheableJsonResponse
   */
  protected $response;

  /**
   * The Resource cache metadata.
   *
   * @var \Drupal\Core\Cache\CacheableMetadata
   */
  protected $responseCache;

  /**
   * Build a normalized success response based on a initialized response.
   *
   * @param \Drupal\Core\Cache\CacheableJsonResponse $response
   *   The resource validator to use.
   * @param string|null $message
   *   The Human message you want to pass.
   * @param array $data
   *   Any other body data to give in your response.
   */
  protected function buildSuccessResponse(CacheableJsonResponse $response, ?string $message = NULL, array $data = []): void {
    /** @var \Symfony\Component\Validator\ConstraintViolationList $errors */
    $this->response->setStatusCode(200);

    $body = [
      'success' => TRUE,
    ];

    if ($message !== NULL) {
      $body['message'] = $message;
    }

    if (!empty($data)) {
      $body['data'] = $data;
    }

    $this->response->setData($body);
  }

  /**
   * Setup a new normalized CacheMetadata.
   *
   * @return \Drupal\Core\Cache\CacheableMetadata
   *   The basic cacheable metadata.
   */
  private function initCacheability(): CacheableMetadata {
    $this->responseCache = new CacheableMetadata();
    $this->responseCache->addCacheContexts(['url.path', 'url.query_args']);

    return $this->responseCache;
  }

  /**
   * Setup a new normalized Response.
   *
   * @return \Drupal\Core\Cache\CacheableJsonResponse
   *   The cacheable response.
   */
  private function initResponse(): CacheableJsonResponse {
    $this->response = new CacheableJsonResponse();
    $this->response->setStatusCode(200);

    return $this->response;
  }

}
