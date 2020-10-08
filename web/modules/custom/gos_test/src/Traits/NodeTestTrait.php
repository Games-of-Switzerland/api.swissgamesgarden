<?php

namespace Drupal\gos_test\Traits;

use Drupal\Core\Language\LanguageInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeTypeInterface;

/**
 * Provides common helper methods for Node module tests.
 */
trait NodeTestTrait {

  /**
   * Bind an existing field storage to a node type.
   *
   * @param string $name
   *   The field name.
   * @param string $node_type
   *   The node type.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function bindNodeField($name, $node_type) {
    $field_storage = FieldStorageConfig::loadByName('node', $name);
    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $node_type,
      'label' => $this->randomMachineName(),
    ]);
    $instance->save();
  }

  /**
   * Returns a new term with random properties in vocabulary $vid.
   *
   * @param \Drupal\node\NodeTypeInterface $type
   *   The node type object.
   * @param array $values
   *   (optional) An array of values to set, keyed by property name. If the
   *   entity type has bundles, the bundle key has to be specified.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The new node object.
   */
  public function createNode(NodeTypeInterface $type, array $values = []) {
    $node = Node::create($values + [
      'title' => $this->randomString(8),
      'type' => $type->id(),
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ]);
    $node->save();

    return $node;
  }

  /**
   * Create a custom field for node.
   *
   * @param string $name
   *   The field name.
   * @param string $type
   *   The field type.
   * @param int $node_type
   *   The node type.
   * @param array $settings
   *   The fields settings.
   * @param int $cardinality
   *   The fields cardinality.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createNodeField($name, $type, $node_type, array $settings = [], $cardinality = 1) {
    $field_storage = FieldStorageConfig::create([
      'field_name' => $name,
      'entity_type' => 'node',
      'type' => $type,
      'settings' => $settings,
      'cardinality' => $cardinality,
    ]);
    $field_storage->save();
    $this->bindNodeField($name, $node_type);
  }

  /**
   * Returns a new node type with random properties.
   *
   * @param string $type
   *   The node type object to create.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeTypeInterface
   *   The newly created node type.
   */
  public function createNodeType($type = NULL) {
    if (!$type) {
      $type = mb_strtolower($this->randomMachineName());
    }

    // Create a node type for testing.
    $type = NodeType::create([
      'type' => $type,
      'name' => $this->randomString(8),
    ]);
    $type->save();

    return $type;
  }

}
