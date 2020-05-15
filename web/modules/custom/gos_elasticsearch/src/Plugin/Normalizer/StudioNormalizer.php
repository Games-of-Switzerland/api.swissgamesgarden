<?php

namespace Drupal\gos_elasticsearch\Plugin\Normalizer;

use Drupal\node\NodeInterface;
use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * Normalizes / denormalizes Drupal Studio nodes into an array structure for ES.
 */
class StudioNormalizer extends ContentEntityNormalizer {

  /**
   * Supported formats.
   *
   * @var array
   */
  protected $format = ['elasticsearch_helper'];

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var array
   */
  protected $supportedInterfaceOrClass = ['Drupal\node\NodeInterface'];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    /** @var \Drupal\node\Entity\Node $object */

    $data = [
      'nid' => $object->id(),
      'name' => $object->getTitle(),
    ];

    if (!$object->get('field_members')->isEmpty()) {
      $members = [];

      foreach ($object->field_members as $member) {
        $members[] = [
          'role' => $member->role ?? NULL,
          'fullname' => isset($member->entity) ? $member->entity->getTitle() : NULL,
        ];
      }

      $data['members'] = $members;
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    if (!parent::supportsNormalization($data, $format)) {
      return FALSE;
    }

    if ($data instanceof NodeInterface && $data->getType() === 'studio') {
      return TRUE;
    }

    return FALSE;
  }

}
