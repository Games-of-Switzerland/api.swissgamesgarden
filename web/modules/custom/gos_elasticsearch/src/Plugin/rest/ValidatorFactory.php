<?php

namespace Drupal\gos_elasticsearch\Plugin\rest;

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validator factory to initialize annotation ready Symfony validator.
 */
class ValidatorFactory {

  /**
   * The class loader.
   *
   * @var \Composer\Autoload\ClassLoader
   */
  protected $classLoader;

  /**
   * Constructs a new Rest validator.
   *
   * @param \Composer\Autoload\ClassLoader $class_loader
   *   The class loader.
   */
  public function __construct(ClassLoader $class_loader) {
    $this->classLoader = $class_loader;
  }

  /**
   * Gets the validator for validating data.
   *
   * @return \Symfony\Component\Validator\Validator\ValidatorInterface
   *   The validator object.
   */
  public function getValidator(): ValidatorInterface {
    AnnotationRegistry::registerLoader([$this->classLoader, 'loadClass']);

    return Validation::createValidatorBuilder()
      ->enableAnnotationMapping()
      ->getValidator();
  }

}
