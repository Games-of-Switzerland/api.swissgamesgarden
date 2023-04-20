<?php

namespace Drupal\Tests\gos_games\Kernel;

use Drupal\gos_game\ReleasesCompiler;
use Drupal\gos_test\Traits\NodeTestTrait;
use Drupal\gos_test\Traits\TaxonomyTestTrait;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;

/**
 * @coversDefaultClass \Drupal\gos_game\ReleasesCompiler
 *
 * @group gos
 * @group gos_game
 * @group gos_game_kernel
 * @group gos_kernel
 *
 * @internal
 */
final class ReleasesCompilerTest extends KernelTestBase {
  use EntityReferenceTestTrait;
  use NodeTestTrait;
  use TaxonomyTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
    'node',
    'taxonomy',
    'field',
    'filter',
    'text',
    'datetime',
    'user',
    'gos_site',
    'gos_game',
  ];

  /**
   * The Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Game for testing.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $testGame;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    /** @var \Drupal\Core\Entity\EntityTypeManager $entityTypeManager */
    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $this->installConfig(['system']);
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('taxonomy_term');
    $this->installSchema('node', 'node_access');

    // Create the taxonomy filter format.
    $this->setupTaxonomy();

    // Setup Taxonomies and Content-Type.
    $this->setupPlatform();
    $this->defaultPlatforms();
    $this->setupGame();

    $this->testGame = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'game',
      'title' => $this->randomString(),
      'field_releases' => [
        [
          'date_value' => '2001-01-01',
          'target_id' => 6,
          'state' => 'prototype',
        ],
        [
          'date_value' => '2003-01-01',
          'target_id' => 4,
          'state' => 'released',
        ],
        [
          'date_value' => '2001-02-02',
          'target_id' => 2,
          'state' => 'pre_release',
        ],
        [
          'date_value' => '1989-01-01',
          'target_id' => 4,
          'state' => 'pre_release',
        ],
        [
          'date_value' => '2000-02-02',
          'target_id' => 1,
          'state' => 'released',
        ],
        [
          'date_value' => '1989-02-02',
          'target_id' => NULL,
          'state' => NULL,
        ],
        [
          'date_value' => '2001-02-02',
          'target_id' => 3,
          'state' => 'canceled',
        ],
        [
          'date_value' => '2009-01-01',
          'target_id' => NULL,
          'state' => 'canceled',
        ],
        [
          'date_value' => NULL,
          'target_id' => 5,
          'state' => 'development',
        ],
        [
          'date_value' => NULL,
          'target_id' => NULL,
          'state' => NULL,
        ],
      ],
    ]);
  }

  /**
   * @covers ::compilePlatforms
   */
  public function testCompilePlatforms(): void {
    $platforms = ReleasesCompiler::compilePlatforms($this->testGame);
    self::assertSame([
      4 => [
        'tid' => 4,
        'name' => 'amiga',
      ],
      2 => [
        'tid' => 2,
        'name' => 'macos',
      ],
      1 => [
        'tid' => 1,
        'name' => 'windows',
      ],
      3 => [
        'tid' => 3,
        'name' => 'linux',
      ],
      5 => [
        'tid' => 5,
        'name' => 'gameboy',
      ],
    ], $platforms);
  }

  /**
   * @covers ::compileYears
   */
  public function testCompileYears(): void {
    $years = ReleasesCompiler::compileYears($this->testGame);
    self::assertSame([
      1989,
      2000,
      2001,
      2003,
      2009,
    ], $years);
  }

  /**
   * @covers ::compileYearsByPlatforms
   */
  public function testCompileYearsByPlatforms(): void {
    $years = ReleasesCompiler::compileYearsByPlatforms($this->testGame);
    self::assertSame([
      'amiga' => [
        'platform' => 'amiga',
        'years' => [
          1989 => '1989',
          2003 => '2003',
        ],
      ],
      'macos' => [
        'platform' => 'macos',
        'years' => [
          2001 => '2001',
        ],
      ],
      'windows' => [
        'platform' => 'windows',
        'years' => [
          2000 => '2000',
        ],
      ],
      'linux' => [
        'platform' => 'linux',
        'years' => [
          2001 => '2001',
        ],
      ],
      'gameboy' => [
        'platform' => 'gameboy',
        'years' => [],
      ],
    ], $years);
  }

  /**
   * @covers ::normalizeReleases
   */
  public function testNormalizeReleases(): void {
    $platforms_by_years = ReleasesCompiler::normalizeReleases($this->testGame);
    self::assertSame([
      'na' => [
        'year' => NULL,
        'platforms' => [
          5 => [
            'name' => 'gameboy',
            'tid' => 5,
            'date' => NULL,
            'state' => 'development',
          ],
        ],
        'states' => [
          'development' => 'development',
        ],
      ],
      1989 => [
        'year' => '1989',
        'platforms' => [
          4 => [
            'name' => 'amiga',
            'tid' => 4,
            'date' => '1989-01-01T00:00:00',
            'state' => 'pre_release',
          ],
        ],
        'states' => ['pre_release' => 'pre_release'],
      ],
      2000 => [
        'year' => '2000',
        'platforms' => [
          1 => [
            'name' => 'windows',
            'tid' => 1,
            'date' => '2000-02-02T00:00:00',
            'state' => 'released',
          ],
        ],
        'states' => ['released' => 'released'],
      ],
      2001 => [
        'year' => '2001',
        'platforms' => [
          2 => [
            'name' => 'macos',
            'tid' => 2,
            'date' => '2001-02-02T00:00:00',
            'state' => 'pre_release',
          ],
          3 => [
            'name' => 'linux',
            'tid' => 3,
            'date' => '2001-02-02T00:00:00',
            'state' => 'canceled',
          ],
        ],
        'states' => [
          'prototype' => 'prototype',
          'pre_release' => 'pre_release',
          'canceled' => 'canceled',
        ],
      ],
      2003 => [
        'year' => '2003',
        'platforms' => [
          4 => [
            'name' => 'amiga',
            'tid' => 4,
            'date' => '2003-01-01T00:00:00',
            'state' => 'released',
          ],
        ],
        'states' => ['released' => 'released'],
      ],
      2009 => [
        'year' => '2009',
        'platforms' => [],
        'states' => ['canceled' => 'canceled'],
      ],
    ], $platforms_by_years);
  }

  /**
   * Setup 5 default Game's Platforms for testing.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface[]
   *   The collection of 5 created Game's platforms.
   */
  private function defaultPlatforms(): array {
    $platform0 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'platforms',
      'tid' => 1,
      'name' => 'Windows',
      'field_slug' => 'windows',
    ]);
    $platform0->save();

    $platform1 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'platforms',
      'tid' => 2,
      'name' => 'Mac',
      'field_slug' => 'macos',
    ]);
    $platform1->save();

    $platform2 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'platforms',
      'tid' => 3,
      'name' => 'Linux',
      'field_slug' => 'linux',
    ]);
    $platform2->save();

    $platform3 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'platforms',
      'tid' => 4,
      'name' => 'Amiga',
      'field_slug' => 'amiga',
    ]);
    $platform3->save();

    $platform4 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'platforms',
      'tid' => 5,
      'name' => 'Gameboy',
      'field_slug' => 'gameboy',
    ]);
    $platform4->save();

    return [$platform0, $platform1, $platform2, $platform3, $platform4];
  }

  /**
   * Setup the Game content type with release field.
   */
  private function setupGame(): void {
    $this->createNodeType('game');
    $this->createNodeField('field_releases', 'release', 'game');
  }

  /**
   * Setup the Game's Platforms vocabulary.
   */
  private function setupPlatform(): void {
    $this->createVocabulary('platforms');
    $this->createTermField('field_slug', 'text', 'platforms');
  }

}
