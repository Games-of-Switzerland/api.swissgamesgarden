<?php

namespace Drupal\gos_site\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'player_default' formatter.
 *
 * @FieldFormatter(
 *     id="player_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "player"
 *     }
 * )
 */
class PlayerFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $item->min . ' ' . $item->max];
    }

    return $elements;
  }

}
