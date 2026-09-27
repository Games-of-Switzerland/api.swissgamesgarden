<?php

namespace Drupal\gos_site\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'Team member' entity field type.
 *
 * Supported settings (below the definition's 'settings' key) are:
 * - target_type: The entity type to reference. Required.
 */
#[FieldType(
  id: 'team_member',
  label: new TranslatableMarkup('Team Member'),
  description: new TranslatableMarkup('A field to define a team member.'),
  category: 'Games of Switzerland',
  default_formatter: 'team_member_default',
  default_widget: 'entity_reference_label',
  list_class: EntityReferenceFieldItemList::class,
)]
class TeamMemberItem extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function defaultFieldSettings() {
    return [
      'handler' => 'default',
      'handler_settings' => [],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function defaultStorageSettings() {
    return [
      'target_type' => \Drupal::moduleHandler()->moduleExists('user') ? 'user' : NULL,
      'role' => '',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function getPreconfiguredOptions() {
    // By returning an empty array we prevent duplicate field list
    // for Content, User and Taxonomy duplicated under Reference.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $role_definition = DataDefinition::create('string')
      ->setLabel(t('Role')->__toString())
      ->setRequired(FALSE);
    $properties['role'] = $role_definition;

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['role'] = [
      'type' => 'varchar',
      'length' => 255,
    ];

    return $schema;
  }

}
