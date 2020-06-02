<?php

namespace Drupal\gos_site\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'store_default' formatter.
 *
 * @FieldFormatter(
 *     id="store_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "store"
 *     }
 * )
 */
class StoreFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $item->store . ' ' . $item->link];
    }

    return $elements;
  }

}
