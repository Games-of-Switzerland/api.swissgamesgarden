<?php

namespace Drupal\gos_elasticsearch\Plugin\rest\ResourceValidator;

// phpcs:disable
// PHPCS does not detect \Assert as use in this file and will remove it. Which
// leads to not working Annotation below.

use Drupal\gos_rest\Plugin\rest\ResourceValidator\BaseValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

// phpcs:enable

/**
 * Serializer class for REST GET parameters of ElasticGamesResource.
 *
 * @see \Drupal\gos_elasticsearch\Plugin\rest\resource\ElasticRealEstateListResource
 */
class ElasticGamesResourceValidator extends BaseValidator {

}
