<?php

namespace Drupal\gos_site\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'Social' entity field type.
 */
#[FieldType(
  id: 'player',
  label: new TranslatableMarkup('Player'),
  description: new TranslatableMarkup('A field to define a number player (min/max) for a game.'),
  category: 'Games of Switzerland',
  default_formatter: 'player_default',
  default_widget: 'player_default',
)]
class PlayerItem extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isEmpty() {
    return empty($this->min) && empty($this->max);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['min'] = DataDefinition::create('string')
      ->setLabel(t('Minimum n° of player')->__toString());

    $properties['max'] = DataDefinition::create('string')
      ->setLabel(t('Maximum n° of player')->__toString());

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      // Columns contains the values that the field will store.
      'columns' => [
        'min' => [
          'type' => 'int',
        ],
        'max' => [
          'type' => 'int',
        ],
      ],
    ];
  }

}
