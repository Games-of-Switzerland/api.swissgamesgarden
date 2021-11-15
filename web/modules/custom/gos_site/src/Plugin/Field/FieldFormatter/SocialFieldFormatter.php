<?php

namespace Drupal\gos_site\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'social_default' formatter.
 *
 * @FieldFormatter(
 *     id="social_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "social"
 *     }
 * )
 */
class SocialFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $item->social_network . ' ' . $item->link];
    }

    return $elements;
  }

}
