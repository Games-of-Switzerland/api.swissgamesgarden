<?php

namespace Drupal\gos_elasticsearch\Plugin\Normalizer;

use Drupal\node\NodeInterface;
use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * Normalizes / denormalizes Drupal People nodes into an array structure for ES.
 */
class PeopleNormalizer extends ContentEntityNormalizer {

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
  public function normalize($object, $format = NULL, array $context = []): null|array|\ArrayObject|bool|float|int|string {
    /** @var \Drupal\node\Entity\Node $object */

    return [
      'uuid' => $object->get('uuid')->value,
      'is_published' => $object->isPublished(),
      'fullname' => $object->getTitle(),
      'bundle' => $object->bundle(),
      'path' => $object->toUrl('canonical')->toString(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL, array $context = []): bool {
    if (!parent::supportsNormalization($data, $format, $context)) {
      return FALSE;
    }

    if ($data instanceof NodeInterface && $data->getType() === 'people') {
      return TRUE;
    }

    return FALSE;
  }

}
