<?php

namespace Drupal\gos_game\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\IntegerItem;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'Game Completeness' field type.
 *
 * @FieldType(
 *     id="gos_game_completeness",
 *     label=@Translation("Game Completeness"),
 *     module="gos_game",
 *     description=@Translation("Game computed score representing the overall data quality score for a single game."),
 *     category=@Translation("Computed"),
 *     default_widget="completeness_widget",
 *     default_formatter="number_integer"
 * )
 */
class CompletenessItem extends IntegerItem {

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    $min = $field_definition->getSetting('min') ?: 0;
    $max = $field_definition->getSetting('max') ?: 999;

    return [
      'value' => random_int($min, $max),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    // Preprocess value only for Game entity.
    $entity = $this->getEntity();

    if ($entity->bundle() !== 'game') {
      return;
    }

    $this->value = random_int(0, 1000);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'value' => DataDefinition::create('integer')
        ->setLabel(t('Score'))
        ->setRequired(TRUE),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'value' => [
          'type' => 'int',
          // Expose the 'unsigned' setting in the field item schema.
          'unsigned' => $field_definition->getSetting('unsigned'),
          // Expose the 'size' setting in the field item schema. For instance,
          // supply 'big' as a value to produce a 'bigint' type.
          'size' => $field_definition->getSetting('size'),
        ],
      ],
    ];
  }

}
