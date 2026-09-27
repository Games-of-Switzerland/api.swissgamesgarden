<?php

namespace Drupal\gos_game\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'release_normalized' field type.
 */
#[FieldType(
  id: 'release_normalized',
  label: new TranslatableMarkup('Release normalized'),
  description: new TranslatableMarkup('Computed normalized releases'),
  no_ui: TRUE,
  list_class: ReleaseNormalizedFieldItemList::class,
)]
class ReleaseNormalizedFieldItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isEmpty() {
    $year = $this->get('year')->getValue();
    $states = $this->get('states')->getValue();
    $platforms = $this->get('platforms')->getValue();

    return ($year === NULL || $year === serialize([])) && ($states === NULL || $states === serialize([])) && ($platforms === NULL || $platforms === serialize([]));
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
  #[\Override]
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [];
  }

}
