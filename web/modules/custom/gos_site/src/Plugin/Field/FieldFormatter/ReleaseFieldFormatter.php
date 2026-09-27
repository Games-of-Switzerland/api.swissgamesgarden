<?php

namespace Drupal\gos_site\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'release_default' formatter.
 */
#[FieldFormatter(
  id: 'release_default',
  label: new TranslatableMarkup('Default'),
  field_types: ['release'],
)]
class ReleaseFieldFormatter extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $item->target_id . ' ' . $item->date_value . ' ' . $item->state];
    }

    return $elements;
  }

}
