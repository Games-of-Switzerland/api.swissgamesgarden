<?php

namespace Drupal\gos_site\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'contextual_link_default' formatter.
 *
 * @FieldFormatter(
 *     id="contextual_link_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "contextual_link"
 *     }
 * )
 */
class ContextualLinkFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $item->type . ' ' . $item->url];
    }

    return $elements;
  }

}
