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

    $data = [
      'nid' => $object->id(),
      'title' => $object->getTitle(),
    ];

    if (!$object->get('field_releases')->isEmpty()) {
      $releases = [];
      foreach ($object->field_releases as $release) {
        $releases[] = [
          'date' => isset($release->date_value) ? $release->date_value : NULL,
          'platform' => isset($release->entity) ? $release->entity->getName() : NULL,
        ];
      }

      $data['releases'] = $releases;
    }

    return $data;
  }

}
