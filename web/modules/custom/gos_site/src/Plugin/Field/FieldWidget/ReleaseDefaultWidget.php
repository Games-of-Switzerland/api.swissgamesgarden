<?php

namespace Drupal\gos_site\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'release_default' widget.
 *
 * @FieldWidget(
 *     id="release_default",
 *     label=@Translation("Release widget"),
 *     field_types={
 *         "release"
 *     }
 * )
 */
class ReleaseDefaultWidget extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $widget = parent::formElement($items, $delta, $element, $form, $form_state);

    $widget['target_id']['#title'] = $this->t('Platform');
    $widget['target_id']['#title_display'] = 'before';

    $widget['date_value'] = [
      '#title' => $this->t('Release date'),
      '#type' => 'date',
      '#default_value' => $items[$delta]->date_value ?? NULL,
      '#weight' => -1,
    ];

    $widget['state'] = [
      '#title' => $this->t('State'),
      '#type' => 'select',
      '#options' => [
        '' => $this->t('- None -'),
        'pre_release' => $this->t('Pre-release'),
        'development' => $this->t('In development'),
        'released' => $this->t('Released'),
        'canceled' => $this->t('Canceled'),
      ],
      '#default_value' => $items[$delta]->state ?? NULL,
      '#weight' => 10,
    ];

    return $widget;
  }

}
