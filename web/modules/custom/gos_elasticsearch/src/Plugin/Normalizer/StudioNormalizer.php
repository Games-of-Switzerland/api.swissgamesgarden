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
   * @var string|string[]
   */
  protected $format = ['elasticsearch_helper'];

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string|array
   */
  protected $supportedInterfaceOrClass = ['Drupal\node\NodeInterface'];

  /**
   * {@inheritdoc}
   */
  public function hasCacheableSupportsMethod(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress ParamNameMismatch
   */
  public function normalize($object, $format = NULL, array $context = []) {
    /** @var \Drupal\node\Entity\Node $object */

    $data = [
      'uuid' => $object->get('uuid')->value,
      'is_published' => $object->isPublished(),
      'name' => $object->getTitle(),
      'bundle' => $object->bundle(),
      'path' => $object->toUrl('canonical')->toString(),
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
  public function supportsNormalization($data, $format = NULL): bool {
    if (!parent::supportsNormalization($data, $format)) {
      return FALSE;
    }

    if ($data instanceof NodeInterface && $data->getType() === 'studio') {
      return TRUE;
    }

    return FALSE;
  }

}
