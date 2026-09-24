<?php

namespace Drupal\gos_game\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of field widget for 'Completeness' field.
 */
#[FieldWidget(
  id: 'completeness_widget',
  label: new TranslatableMarkup('Completeness'),
  field_types: ['completeness'],
)]
class CompletenessWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = $element + [
      '#type' => 'textfield',
      '#description' => $this->t('This is an auto-filled field. The score value will be calculated on each save/update of the Game entity.'),
      '#value' => !empty($items[$delta]->value) ? $items[$delta]->value : '',
      // Never let anyone change the field value manually.
      '#disabled' => TRUE,
    ];

    return $element;
  }

}
