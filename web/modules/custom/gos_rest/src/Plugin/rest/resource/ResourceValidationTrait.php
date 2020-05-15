<?php

namespace Drupal\gos_rest\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\gos_rest\Plugin\rest\ResourceValidator\BaseValidator;

/**
 * Provides methods to validate Resource.
 */
trait ResourceValidationTrait {

  /**
   * The Resource response that will be returned.
   *
   * @var \Drupal\Core\Cache\CacheableJsonResponse
   */
  protected $response;

  /**
   * Symfony Validator component.
   *
   * @var \Symfony\Component\Validator\Validator\ValidatorInterface
   */
  protected $validator;

  /**
   * Build a normalized error(s) response based on a resource validator.
   *
   * @param \Drupal\gos_rest\Plugin\rest\ResourceValidator\BaseValidator $resource_validator
   *   The resource validator to use.
   *
   * @return \Drupal\Core\Cache\CacheableJsonResponse
   *   The basic cacheable error response.
   */
  protected function buildValidatorErrorResponse(BaseValidator $resource_validator): CacheableJsonResponse {
    /** @var \Symfony\Component\Validator\ConstraintViolationList $errors */
    $errors = $this->validator->validate($resource_validator);

    $this->response->setStatusCode(400);

    $body = [
      'message' => $this->t('Something went wrong.'),
      'errors' => [],
    ];

    foreach ($errors as $error) {
      $property = $error->getPropertyPath();
      $body['errors'][$property][] = $error->getMessage();
    }

    $this->response->setData($body);

    return $this->response;
  }

  /**
   * Ensure the given parameters are valid parameters for the Resource.
   *
   * @param \Drupal\gos_rest\Plugin\rest\ResourceValidator\BaseValidator $resource_validator
   *   The resource validator to use.
   *
   * @return bool
   *   Does the given resource validator is valid ?
   */
  protected function isValid(BaseValidator $resource_validator): bool {
    /** @var \Symfony\Component\Validator\ConstraintViolationList $errors */
    $errors = $this->validator->validate($resource_validator);

    return $errors->count() === 0;
  }

}
