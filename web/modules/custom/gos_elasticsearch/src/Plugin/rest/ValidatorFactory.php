<?php

namespace Drupal\gos_elasticsearch\Plugin\rest;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validator factory to initialize annotation ready Symfony validator.
 */
class ValidatorFactory {

  /**
   * Gets the validator for validating data.
   *
   * @return \Symfony\Component\Validator\Validator\ValidatorInterface
   *   The validator object.
   */
  public function getValidator(): ValidatorInterface {
    return Validation::createValidatorBuilder()
      ->enableAnnotationMapping()
      ->getValidator();
  }

}

