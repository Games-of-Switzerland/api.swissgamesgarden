<?php

namespace Drupal\gos_site\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'Store' entity field type.
 */
#[FieldType(
  id: 'store',
  label: new TranslatableMarkup('Store'),
  description: new TranslatableMarkup('A field to define a store with a link.'),
  category: 'Games of Switzerland',
  default_formatter: 'store_default',
  default_widget: 'store_default',
)]
class StoreItem extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isEmpty() {
    return empty($this->store) || empty($this->link);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['store'] = DataDefinition::create('string')
      ->setLabel(t('Store')->__toString());

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
        'store' => [
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
