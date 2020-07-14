<?php

namespace Drupal\gos_site\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'social_default' widget.
 *
 * @FieldWidget(
 *     id="social_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "social"
 *     }
 * )
 */
class SocialDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['social'] = [
      '#title' => $this->t('Social Network'),
      '#type' => 'select',
      '#options' => [
        '' => $this->t('- None -'),
        'twitter' => $this->t('Twitter'),
        'facebook' => $this->t('Facebook'),
        'Instagram' => $this->t('Instagram'),
      ],
      '#default_value' => $items[$delta]->social_network ?? NULL,
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
