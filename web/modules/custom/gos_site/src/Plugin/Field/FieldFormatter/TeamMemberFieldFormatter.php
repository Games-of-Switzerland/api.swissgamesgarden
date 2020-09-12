<?php

namespace Drupal\gos_site\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;

/**
 * Plugin implementation of the 'team_member_default' formatter.
 *
 * @FieldFormatter(
 *     id="team_member_default",
 *     label=@Translation("Default"),
 *     field_types={
 *         "team_member"
 *     }
 * )
 */
class TeamMemberFieldFormatter extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $item->entity->getTitle()];
    }

    return $elements;
  }

}
