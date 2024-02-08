<?php

namespace Drupal\gos_elasticsearch\Plugin\Normalizer\Traits;

use Drupal\Core\Field\FieldItemListInterface;

/**
 * Normalizes / denormalizes Drupal Property nodes to a structure for ES.
 */
trait NormalizerImagesDerivativesTrait {

  /**
   * The image style entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $imageStyleStorage;

  /**
   * List of image styles IDs to generate.
   *
   * @var string[]
   */
  private $imageStyles = [
    '3x2_330x220',
    '3x2_660x440',
    'placeholder_30x30',
  ];

  /**
   * Generate Images derivatives.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $field_image
   *   The image field which contains file image.
   * @param string[] $styles
   *   A collection of image styles IDs to generate derivative for.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   *
   * @return array
   *   The collection of image derivative URL including the source image URL.
   */
  private function generateImagesDerivatives(FieldItemListInterface $field_image, array $styles): array {
    $medias = [];

    foreach ($field_image as $file) {
      // Skip file entity without real file on filesystem.
      if (!$file->entity) {
        continue;
      }

      /** @var \Drupal\image\Plugin\Field\FieldType\ImageItem $file */
      $media = [
        'width' => $file->get('width')->getValue(),
        'height' => $file->get('height')->getValue(),
        'href' => $file->entity->createFileUrl(),
        'links' => [],
      ];

      foreach ($styles as $style_id) {
        $uri = $file->entity->uri->value;

        /** @var \Drupal\image\ImageStyleInterface|null $style */
        $style = $this->imageStyleStorage->load($style_id);

        if (!$style) {
          continue;
        }

        $dimensions = [];
        $style->transformDimensions($dimensions, $uri);

        $media['links'][$style_id] = [
          'name' => $style_id,
          'width' => $dimensions['width'] ?? NULL,
          'height' => $dimensions['height'] ?? NULL,
          'href' => $style->buildUrl($uri),
        ];
      }

      $medias[] = $media;
    }

    return $medias;
  }

}
