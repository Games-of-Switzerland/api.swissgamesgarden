<?php

namespace Drupal\gos_site\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'Store' entity field type.
 *
 * @FieldType(
 *     id="contextual_link",
 *     label=@Translation("Contextual Link"),
 *     description=@Translation("A field to define a contextual link."),
 *     category=@Translation("Games of Switzerland"),
 *     default_formatter="contextual_link_default",
 *     default_widget="contextual_link_default",
 * )
 */
class ContextualLinkItem extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return empty($this->type) || empty($this->url);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['type'] = DataDefinition::create('string')
      ->setLabel(t('Type')->__toString());

    $properties['url'] = DataDefinition::create('string')
      ->setLabel(t('Url')->__toString());

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      // Columns contains the values that the field will store.
      'columns' => [
        'type' => [
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
        'url' => [
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
      ],
    ];
  }

}
