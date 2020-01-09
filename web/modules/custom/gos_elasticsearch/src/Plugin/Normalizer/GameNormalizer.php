<?php

namespace Drupal\gos_elasticsearch\Plugin\Normalizer;

use Drupal\node\NodeInterface;
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
  public function supportsNormalization($data, $format = NULL) {
    if (!parent::supportsNormalization($data, $format)) {
      return FALSE;
    }

    if ($data instanceof NodeInterface && $data->getType() === 'game') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    /** @var \Drupal\node\Entity\Node $object */

    $data = [
      'nid' => $object->id(),
      'title' => $object->getTitle(),
      'desc' => !$object->get('body')->isEmpty() ? strip_tags($object->body->value) : NULL,
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

    // Handle studios names.
    if (!$object->get('field_studios')->isEmpty()) {
      $studios = [];
      foreach ($object->field_studios as $studio) {
        $studios[] = [
          'name' => $studio->entity->title->value,
          'id' => $studio->target_id,
        ];
      }

      $data['studios'] = $studios;
    }

    return $data;
  }

}
