<?php

namespace Drupal\gos_test\Traits;

use Drupal\Core\Language\LanguageInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\filter\Entity\FilterFormat;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Provides common helper methods for Taxonomy module tests.
 */
trait TaxonomyTestTrait {

  /**
   * Bind a taxonomy term field to a vocabulary.
   *
   * @param string $name
   *   Field name.
   * @param string $vocabulary
   *   Vocabulary id.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function bindTermField($name, $vocabulary) {
    $field_storage = FieldStorageConfig::loadByName('taxonomy_term', $name);
    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $vocabulary,
      'label' => $this->randomMachineName(),
    ]);
    $instance->save();
  }

  /**
   * Returns a new term with random properties in vocabulary $vid.
   *
   * @param \Drupal\taxonomy\Entity\Vocabulary $vocabulary
   *   The vocabulary object.
   * @param array $values
   *   (optional) An array of values to set, keyed by property name. If the
   *   entity type has bundles, the bundle key has to be specified.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\taxonomy\TermInterface
   *   The new taxonomy term object.
   */
  public function createTerm(Vocabulary $vocabulary, array $values = []) {
    $filter_formats = filter_formats();
    $format = array_pop($filter_formats);

    $term = Term::create($values + [
      'name' => $this->randomMachineName(),
      'description' => [
        'value' => $this->randomMachineName(),
        // Use the first available text format.
        'format' => $format->id(),
      ],
      'vid' => $vocabulary->id(),
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ]);
    $term->save();

    return $term;
  }

  /**
   * Create a custom field for taxonomy.
   *
   * @param string $name
   *   The field name.
   * @param string $type
   *   The field type.
   * @param int $vocabulary
   *   The vocabulary id.
   * @param array $settings
   *   The fields settings.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createTermField($name, $type, $vocabulary, array $settings = []) {
    $field_storage = FieldStorageConfig::create([
      'field_name' => $name,
      'entity_type' => 'taxonomy_term',
      'type' => $type,
      'settings' => $settings,
    ]);
    $field_storage->save();
    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $vocabulary,
      'label' => $this->randomMachineName(),
    ]);
    $instance->save();
  }

  /**
   * Returns a new vocabulary with random properties.
   *
   * @param string $vid
   *   The vocabulary name object to create.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\taxonomy\VocabularyInterface
   *   The newly created vocabulary.
   */
  public function createVocabulary($vid = NULL) {
    if ($vid === NULL) {
      $vid = mb_strtolower($this->randomMachineName());
    }

    // Create a vocabulary.
    $vocabulary = Vocabulary::create([
      'name' => $this->randomMachineName(),
      'description' => $this->randomMachineName(),
      'vid' => $vid,
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
      'weight' => mt_rand(0, 10),
    ]);
    $vocabulary->save();

    return $vocabulary;
  }

  /**
   * Create the default Filter Format necessary for taxonomy terms.
   */
  public function setupTaxonomy() {
    $filter_formats = filter_formats();
    $format = array_pop($filter_formats);

    if (!empty($format)) {
      return;
    }

    $format = FilterFormat::create([
      'format' => 'test',
      'name' => 'Test format',
      'weight' => 1,
      'filters' => [
        'filter_html_escape' => ['status' => TRUE],
      ],
    ]);
    $format->save();
  }

}
