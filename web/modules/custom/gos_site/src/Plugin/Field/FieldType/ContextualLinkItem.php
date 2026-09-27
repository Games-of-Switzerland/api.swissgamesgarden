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
  id: 'contextual_link',
  label: new TranslatableMarkup('Contextual Link'),
  description: new TranslatableMarkup('A field to define a contextual link.'),
  category: 'Games of Switzerland',
  default_formatter: 'contextual_link_default',
  default_widget: 'contextual_link_default',
)]
class ContextualLinkItem extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isEmpty() {
    return empty($this->type) || empty($this->url);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
  #[\Override]
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
