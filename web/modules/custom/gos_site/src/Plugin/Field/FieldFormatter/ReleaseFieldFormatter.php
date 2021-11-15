<?php

namespace Drupal\gos_site\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;

/**
 * Plugin implementation of the 'release_default' formatter.
 *
 * @FieldFormatter(
 *     id="release_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "release"
 *     }
 * )
 */
class ReleaseFieldFormatter extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $item->target_id . ' ' . $item->date_value . ' ' . $item->state];
    }

    return $elements;
  }

}
