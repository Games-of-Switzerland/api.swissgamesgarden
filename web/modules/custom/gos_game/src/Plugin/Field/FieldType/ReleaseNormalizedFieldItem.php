<?php

namespace Drupal\gos_game\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'release_normalized' field type.
 *
 * @FieldType(
 *     id="release_normalized",
 *     label=@Translation("Release normalized"),
 *     description=@Translation("Computed normalized releases"),
 *     no_ui=TRUE,
 *     list_class="\Drupal\gos_game\Plugin\Field\FieldType\ReleaseNormalizedFieldItemList",
 * )
 */
class ReleaseNormalizedFieldItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $year = $this->get('year')->getValue();
    $states = $this->get('states')->getValue();
    $platforms = $this->get('platforms')->getValue();

    return ($year === NULL || $year === serialize([])) && ($states === NULL || $states === serialize([])) && ($platforms === NULL || $platforms === serialize([]));
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['year'] = DataDefinition::create('string')
      ->setLabel(t('Year')->__toString())
      ->setRequired(TRUE);

    $properties['platforms'] = DataDefinition::create('any')
      ->setLabel(t('Platforms')->__toString())
      ->setRequired(TRUE);

    $properties['states'] = DataDefinition::create('any')
      ->setLabel(t('States')->__toString())
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [];
  }

}
