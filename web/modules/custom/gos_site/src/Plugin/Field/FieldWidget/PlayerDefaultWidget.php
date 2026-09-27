<?php

namespace Drupal\gos_site\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'player_default' widget.
 */
#[FieldWidget(
  id: 'player_default',
  label: new TranslatableMarkup('Default'),
  field_types: ['player'],
)]
class PlayerDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['min'] = [
      '#title' => $this->t('Minimum'),
      '#type' => 'textfield',
      '#field_suffix' => $this->t('player(s)'),
      '#default_value' => $items[$delta]->min ?? NULL,
    ];

    $element['max'] = [
      '#title' => $this->t('Maximum'),
      '#type' => 'textfield',
      '#field_suffix' => $this->t('player(s)'),
      '#default_value' => $items[$delta]->max ?? NULL,
    ];

    return $element;
  }

}
