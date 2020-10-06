<?php

namespace Drupal\Tests\gos_game\Unit;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\gos_game\CompletenessCalculator;
use Drupal\node\NodeInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\gos_game\CompletenessCalculator
 *
 * @group gos
 * @group gos_game
 * @group gos_game_unit
 *
 * @internal
 */
final class CompletenessCalculatorTest extends UnitTestCase {

  /**
   * Game for testing.
   *
   * @var \Prophecy\Prophecy\ObjectProphecy
   */
  protected $testGame;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->testGame = $this->prophesize(NodeInterface::class);

    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(FALSE)->shouldBeCalled();

    $this->testGame->get('body')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('body')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_webiste')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_webiste')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_studios')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_studios')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_members')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_members')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_locations')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_locations')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_publishers')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_publishers')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_releases')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_releases')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_languages')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_languages')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_genres')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_genres')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_awards')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_awards')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_stores')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_stores')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_article_links')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_article_links')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_sources')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_sources')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_images')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_images')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_social_networks')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_social_networks')->willReturn(TRUE)->shouldBeCalled();
  }

  /**
   * @covers ::calculation
   */
  public function testCalculation() {
    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(8643, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithMembersOnly() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_studios')->willReturn($field_item->reveal());

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(7643, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithoutImages() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_images')->willReturn($field_item->reveal());

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(5643, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithoutMembersNeitherStudios() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_studios')->willReturn($field_item->reveal());
    $this->testGame->get('field_members')->willReturn($field_item->reveal());

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(5143, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithoutReleases() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_releases')->willReturn($field_item->reveal());

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(6893, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithStudiosOnly() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_members')->willReturn($field_item->reveal());

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(7643, $score);
  }

}
