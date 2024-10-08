<?php

namespace Drupal\gos_site\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'Release' entity field type.
 *
 * Supported settings (below the definition's 'settings' key) are:
 * - target_type: The entity type to reference. Required.
 *
 * @FieldType(
 *     id="release",
 *     label=@Translation("Release"),
 *     description=@Translation("A field to define a release."),
 *     category=@Translation("Games of Switzerland"),
 *     default_formatter="release_default",
 *     default_widget="entity_reference_label",
 *     list_class="\Drupal\Core\Field\EntityReferenceFieldItemList",
 * )
 */
class ReleaseItem extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'handler' => 'default',
      'handler_settings' => [],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'target_type' => \Drupal::moduleHandler()->moduleExists('taxonomy') ? 'taxonomy_term' : NULL,
      'date' => '',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    // By returning an empty array we prevent duplicate field list
    // for Content, User and Taxonomy duplicated under Reference.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $date_value_definition = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('Date value')->__toString());
    $properties['date_value'] = $date_value_definition;

    $date_definition = DataDefinition::create('any')
      ->setLabel(t('The computed DateTime object')->__toString())
      ->setComputed(TRUE)
      ->setClass('\Drupal\datetime\DateTimeComputed')
      ->setSetting('date source', 'date_value');
    $properties['date'] = $date_definition;

    $properties['state'] = DataDefinition::create('string')
      ->setLabel(t('State')->__toString());

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['date_value'] = [
      'type' => 'varchar',
      'length' => 20,
    ];
    $schema['columns']['state'] = [
      'type' => 'text',
      'size' => 'tiny',
      'not null' => FALSE,
    ];

    return $schema;
  }

}
