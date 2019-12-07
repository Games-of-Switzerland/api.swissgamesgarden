<?php

namespace Drupal\gos_site\Plugin\Field\FieldType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;

/**
 * Defines the 'Release' entity field type.
 *
 * Supported settings (below the definition's 'settings' key) are:
 * - target_type: The entity type to reference. Required.
 *
 * @FieldType(
 *   id = "release",
 *   label = @Translation("Release"),
 *   description = @Translation("A field to define a release."),
 *   category = @Translation("Games of Switzerland"),
 *   default_formatter = "release_default",
 *   default_widget = "release_default",
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 * )
 */
class ReleaseItem extends EntityReferenceItem {

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
  public static function defaultFieldSettings() {
    return [
      'handler' => 'default',
      'handler_settings' => [],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $date_value_definition = DataDefinition::create('datetime_iso8601')
      ->setLabel(new TranslatableMarkup('Date value'));
    $properties['date_value'] = $date_value_definition;

    $date_definition = DataDefinition::create('any')
      ->setLabel(new TranslatableMarkup('Computed date'))
      ->setLabel(new TranslatableMarkup('The computed DateTime object.'))
      ->setComputed(TRUE)
      ->setClass('\Drupal\datetime\DateTimeComputed')
      ->setSetting('date source', 'date_value');
    $properties['date'] = $date_definition;

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

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    // By returning an empty array we prevent duplicate field list
    // for Content, User and Taxonomy duplicated under Reference.
    return [];
  }

}
