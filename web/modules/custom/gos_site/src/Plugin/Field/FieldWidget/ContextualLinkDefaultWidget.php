<?php

namespace Drupal\gos_site\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'contextual_link_default' widget.
 *
 * @FieldWidget(
 *     id="contextual_link_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "contextual_link"
 *     }
 * )
 */
class ContextualLinkDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['type'] = [
      '#title' => $this->t('Type'),
      '#type' => 'select',
      '#options' => [
        '' => $this->t('- None -'),
        'preskit' => $this->t('Preskit'),
        'online_play' => $this->t('Online Play'),
        'download_page' => $this->t('Download page'),
        'direct_download' => $this->t('Direct Download'),
        'devlog' => $this->t('Devlog'),
        'box_art' => $this->t('Box Art'),
      ],
      '#default_value' => $items[$delta]->type ?? NULL,
    ];

    $element['url'] = [
      '#title' => $this->t('Url'),
      '#type' => 'url',
      '#default_value' => $items[$delta]->url ?? NULL,
      '#attributes' => [
        'placeholder' => 'https://',
      ],
    ];

    return $element;
  }

}
