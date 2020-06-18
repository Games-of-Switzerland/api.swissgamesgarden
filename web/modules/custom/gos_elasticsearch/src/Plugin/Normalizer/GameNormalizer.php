<?php

namespace Drupal\gos_elasticsearch\Plugin\Normalizer;

use Drupal\node\NodeInterface;
use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * Normalizes / denormalizes Drupal Game nodes into an array structure for ES.
 */
class GameNormalizer extends ContentEntityNormalizer {

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
      'uuid' => $object->get('uuid')->value,
      'title' => $object->getTitle(),
      'desc' => !$object->get('body')->isEmpty() ? strip_tags($object->body->value) : NULL,
      'is_published' => $object->isPublished(),
      'bundle' => $object->bundle(),
      'path' => $object->toUrl('canonical')->toString(),
      'changed' => $object->getChangedTime(),
    ];

    if (!$object->get('field_releases')->isEmpty()) {
      $releases = [];

      foreach ($object->field_releases as $release) {
        $releases[] = [
          'date' => isset($release->date_value) ? $release->date_value : NULL,
          'platform_slug' => isset($release->entity) ? $release->entity->get('field_slug')->value : NULL,
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
          'uuid' => $studio->entity->get('uuid')->value,
        ];
      }

      $data['studios'] = $studios;
    }

    // Handle genres.
    if (!$object->get('field_genres')->isEmpty()) {
      $genres = [];

      foreach ($object->field_genres as $genre) {
        $genres[] = [
          'slug' => $genre->entity->get('field_slug')->value,
        ];
      }

      $data['genres'] = $genres;
    }

    // Handle stores.
    if (!$object->get('field_stores')->isEmpty()) {
      $genres = [];

      foreach ($object->field_stores as $store) {
        $genres[] = [
          'slug' => $store->store,
          'link' => $store->link,
        ];
      }

      $data['stores'] = $genres;
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

    if ($data instanceof NodeInterface && $data->getType() === 'game') {
      return TRUE;
    }

    return FALSE;
  }

}
