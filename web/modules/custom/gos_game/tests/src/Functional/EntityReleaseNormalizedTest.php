<?php

namespace Drupal\Tests\gos_game\Functional;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\gos_test\Traits\TaxonomyTestTrait;
use Drupal\Tests\entity_test\Functional\Rest\EntityTestResourceTestBase;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;
use Drupal\Tests\rest\Functional\AnonResourceTestTrait;

/**
 * Verify that the JSON output from JsonApi works as intended.
 *
 * @covers \Drupal\gos_game\Plugin\Field\FieldType\ReleaseNormalizedFieldItem
 * @covers \Drupal\gos_game\Plugin\Field\FieldType\ReleaseNormalizedFieldItemList
 *
 * @group gos
 * @group gos_game
 * @group gos_game_functional
 *
 * @internal
 */
final class EntityReleaseNormalizedTest extends EntityTestResourceTestBase {
  use AnonResourceTestTrait;
  use EntityReferenceTestTrait;
  use TaxonomyTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['taxonomy', 'filter', 'gos_site', 'gos_game'];

  /**
   * Whether the Release field has been added to the Test Entity.
   *
   * @var bool
   */
  protected $addedFields = FALSE;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    // Create the taxonomy filter format.
    $this->setupTaxonomy();

    // Setup Taxonomies and Content-Type.
    $this->setupPlatform();
    $this->defaultPlatforms();
  }

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    /** @var \Drupal\Core\Entity\EntityTypeManager $entityTypeManager */
    $this->entityTypeManager = $this->container->get('entity_type.manager');

    // Add the fields here rather than in ::setUp() because they need to be
    // created before the entity is, and this method is called from
    // parent::setUp().
    if (!$this->addedFields) {
      $this->addedFields = TRUE;

      FieldStorageConfig::create([
        'entity_type' => 'entity_test',
        'field_name' => 'field_releases',
        'type' => 'release',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      ])->save();

      FieldConfig::create([
        'entity_type' => 'entity_test',
        'field_name' => 'field_releases',
        'bundle' => 'entity_test',
      ])->save();
    }

    $entity_test = $this->entityTypeManager->getStorage('entity_test')->create([
      'name' => 'Llama',
      'type' => 'entity_test',
      'field_releases' => [
        ['date_value' => '2001-02-02', 'target_id' => 2, 'state' => 'released'],
        ['date_value' => '2000-02-02', 'target_id' => 1, 'state' => 'released'],
        ['date_value' => '1989-02-02', 'target_id' => NULL, 'state' => NULL],
        ['date_value' => '2001-02-02', 'target_id' => 3, 'state' => 'canceled'],
        ['date_value' => '2009-01-01', 'target_id' => NULL, 'state' => NULL],
        ['date_value' => NULL, 'target_id' => 3, 'state' => 'development'],
      ],
    ]);
    $entity_test->setOwnerId(0);
    $entity_test->save();

    return $entity_test;
  }

  /**
   * {@inheritdoc}
   */
  protected function getExpectedNormalizedEntity() {
    return parent::getExpectedNormalizedEntity() + [
      'field_releases' => [
        [
          'date_value' => '2001-02-02T11:00:00+11:00',
          'state' => 'released',
          'target_id' => 2,
          'target_type' => 'taxonomy_term',
          'target_uuid' => '36f310ed-0649-438b-bb39-42d6ea16e361',
          'url' => '/taxonomy/term/2',
        ],
        [
          'date_value' => '2000-02-02T11:00:00+11:00',
          'state' => 'released',
          'target_id' => 1,
          'target_type' => 'taxonomy_term',
          'target_uuid' => '9832c69f-77ae-4119-a8c5-cdaf51e477fa',
          'url' => '/taxonomy/term/1',
        ],
        [
          'date_value' => '2001-02-02T11:00:00+11:00',
          'state' => 'canceled',
          'target_id' => 3,
          'target_type' => 'taxonomy_term',
          'target_uuid' => 'da1a3a96-baa8-4b57-8f0f-35aad71c8d29',
          'url' => '/taxonomy/term/3',
        ],
        [
          'date_value' => NULL,
          'state' => 'development',
          'target_id' => 3,
          'target_type' => 'taxonomy_term',
          'target_uuid' => 'da1a3a96-baa8-4b57-8f0f-35aad71c8d29',
          'url' => '/taxonomy/term/3',
        ],
      ],
      'releases_normalized' => [
        [
          'platforms' => [
            3 => [
              'date' => NULL,
              'name' => 'linux',
              'state' => 'development',
              'tid' => '3',
            ],
          ],
          'year' => NULL,
          'states' => ['development' => 'development'],
        ],
        [
          'platforms' => [
            1 => [
              'date' => '2000-02-02T00:00:00',
              'name' => 'windows',
              'tid' => '1',
              'state' => 'released',
            ],
          ],
          'year' => '2000',
          'states' => ['released' => 'released'],
        ],
        [
          'platforms' => [
            2 => [
              'date' => '2001-02-02T00:00:00',
              'name' => 'macos',
              'tid' => '2',
              'state' => 'released',
            ],
            3 => [
              'date' => '2001-02-02T00:00:00',
              'name' => 'linux',
              'tid' => '3',
              'state' => 'canceled',
            ],
          ],
          'year' => '2001',
          'states' => ['released' => 'released', 'canceled' => 'canceled'],
        ],
      ],
    ];
  }

  /**
   * Setup 3 default Game's Platforms for testing.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   The collection of 3 created Game's platforms.
   */
  private function defaultPlatforms(): array {
    $platform0 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'platforms',
      'tid' => 1,
      'name' => 'Windows',
      'field_slug' => 'windows',
      'uuid' => '9832c69f-77ae-4119-a8c5-cdaf51e477fa',
    ]);
    $platform0->save();

    $platform1 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'platforms',
      'tid' => 2,
      'name' => 'Mac',
      'field_slug' => 'macos',
      'uuid' => '36f310ed-0649-438b-bb39-42d6ea16e361',
    ]);
    $platform1->save();

    $platform2 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'platforms',
      'tid' => 3,
      'name' => 'Linux',
      'field_slug' => 'linux',
      'uuid' => 'da1a3a96-baa8-4b57-8f0f-35aad71c8d29',
    ]);
    $platform2->save();

    return [$platform0, $platform1, $platform2];
  }

  /**
   * Setup the Game's Platforms vocabulary.
   */
  private function setupPlatform(): void {
    $this->createVocabulary('platforms');
    $this->createTermField('field_slug', 'text', 'platforms');
  }

}
