<?php

namespace Drupal\gos_site\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'team_member_default' widget.
 *
 * @FieldWidget(
 *   id = "team_member_default",
 *   label = @Translation("Team Member widget"),
 *   field_types = {
 *     "team_member"
 *   }
 * )
 */
class TeamMemberDefaultWidget extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $widget = parent::formElement($items, $delta, $element, $form, $form_state);

    $widget['target_id']['#title'] = $this->t('Who');
    $widget['target_id']['#title_display'] = 'before';

    $widget['role'] = [
      '#title' => $this->t('Role'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->role) ? $items[$delta]->role : NULL,
      '#weight' => 1,
    ];
    return $widget;
  }

}
