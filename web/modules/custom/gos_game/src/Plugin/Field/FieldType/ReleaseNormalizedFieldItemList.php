<?php

namespace Drupal\gos_game\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\gos_game\ReleasesCompiler;

/**
 * Represents the computed release values for an release entry.
 */
class ReleaseNormalizedFieldItemList extends FieldItemList {
  use ComputedItemListTrait;

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress PropertyTypeCoercion
   * @psalm-suppress ArgumentTypeCoercion
   */
  protected function computeValue() {
    $entity = $this->getEntity();

    $platforms_by_years = ReleasesCompiler::compilePlatformsByYears($entity);
    $this->list = [];
    $offset = 0;

    foreach ($platforms_by_years as $platforms_by_year) {
      $item = [
        'year' => $platforms_by_year['year'],
        'platforms' => $platforms_by_year['platforms'],
      ];
      $this->list[] = $this->createItem($offset, $item);
      ++$offset;
    }
  }

}
