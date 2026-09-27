<?php

namespace Drupal\gos_site\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'player_default' formatter.
 */
#[FieldFormatter(
  id: 'player_default',
  label: new TranslatableMarkup('Default'),
  field_types: ['player'],
)]
class PlayerFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $item->min . ' ' . $item->max];
    }

    return $elements;
  }

}
