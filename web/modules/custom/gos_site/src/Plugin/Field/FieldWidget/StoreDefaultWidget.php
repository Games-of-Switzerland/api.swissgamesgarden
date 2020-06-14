<?php

namespace Drupal\gos_site\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'store_default' widget.
 *
 * @FieldWidget(
 *     id="store_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "store"
 *     }
 * )
 */
class StoreDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['store'] = [
      '#title' => $this->t('Store'),
      '#type' => 'select',
      '#options' => [
        '' => $this->t('- None -'),
        'apple_store' => $this->t('Apple Store'),
        'steam' => $this->t('Steam'),
        'amazon' => $this->t('Amazon'),
        'itchio' => $this->t('Itch.io'),
        'facebook' => $this->t('Facebook App'),
        'epic' => $this->t('EPIC Store'),
        'playstation' => $this->t('Playstation Store'),
        'xbox' => $this->t('Xbox Marketplace'),
        'nintendo' => $this->t('Nintendo'),
        'microsoft_store' => $this->t('Microsoft Store'),
        'oculus' => $this->t('Oculus'),
        'google_play_store' => $this->t('Google Play Store'),
        'gog' => $this->t('GOG.com'),
        'custom' => $this->t('Custom'),
      ],
      '#default_value' => $items[$delta]->store ?? NULL,
    ];

    $element['link'] = [
      '#title' => $this->t('Link'),
      '#type' => 'url',
      '#default_value' => $items[$delta]->link ?? NULL,
      '#attributes' => [
        'placeholder' => 'https://',
      ],
    ];

    return $element;
  }

}
