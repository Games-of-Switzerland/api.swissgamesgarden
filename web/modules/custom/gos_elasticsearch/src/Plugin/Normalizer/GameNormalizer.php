<?php

namespace Drupal\gos_elasticsearch\Plugin\Normalizer;

use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * Normalizes / denormalizes Drupal Game nodes into an array structure for ES.
 */
class GameNormalizer extends ContentEntityNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var array
   */
  protected $supportedInterfaceOrClass = ['Drupal\node\NodeInterface'];

  /**
   * Supported formats.
   *
   * @var array
   */
  protected $format = ['elasticsearch_helper'];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    /** @var \Drupal\node\Entity\Node $object */

    return [
      'nid' => $object->id(),
      'title' => $object->getTitle(),
    ];
  }

}
