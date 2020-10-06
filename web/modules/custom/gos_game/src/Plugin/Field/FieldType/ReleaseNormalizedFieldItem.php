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
    $value = $this->get('year')->getValue();

    return $value === NULL || $value === serialize([]);
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
      ->setLabel(t('Name')->__toString())
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
