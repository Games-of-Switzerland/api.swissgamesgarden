<?php

namespace Drupal\gos_site\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'Social' entity field type.
 *
 * @FieldType(
 *     id="social",
 *     label=@Translation("Social"),
 *     description=@Translation("A field to define a social link."),
 *     category=@Translation("Games of Switzerland"),
 *     default_formatter="social_default",
 *     default_widget="social_default",
 * )
 */
class SocialItem extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return empty($this->social_network) || empty($this->link);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['social_network'] = DataDefinition::create('string')
      ->setLabel(t('Social Network')->__toString());

    $properties['link'] = DataDefinition::create('string')
      ->setLabel(t('Link')->__toString());

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      // Columns contains the values that the field will store.
      'columns' => [
        'social_network' => [
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
        'link' => [
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
      ],
    ];
  }

}
