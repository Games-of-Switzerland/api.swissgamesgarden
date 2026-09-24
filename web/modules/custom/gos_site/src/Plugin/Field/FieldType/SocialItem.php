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
  id: 'social',
  label: new TranslatableMarkup('Social'),
  description: new TranslatableMarkup('A field to define a social link.'),
  category: 'Games of Switzerland',
  default_formatter: 'social_default',
  default_widget: 'social_default',
)]
class SocialItem extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isEmpty() {
    return empty($this->social_network) || empty($this->link);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
  #[\Override]
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
