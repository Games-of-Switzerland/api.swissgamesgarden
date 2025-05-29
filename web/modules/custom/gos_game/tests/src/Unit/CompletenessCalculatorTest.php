<?php

namespace Drupal\Tests\gos_game\Unit;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\gos_game\CompletenessCalculator;
use Drupal\node\NodeInterface;

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
  protected function setUp(): void {
    parent::setUp();
    $this->testGame = $this->prophesize(NodeInterface::class);

    $field_item_not_empty = $this->prophesize(FieldItemInterface::class);
    $field_item_not_empty->isEmpty()->willReturn(FALSE)->shouldBeCalled();

    $field_item_empty = $this->prophesize(FieldItemInterface::class);
    $field_item_empty->isEmpty()->willReturn(TRUE)->shouldBeCalled();

    $this->testGame->get('body')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('body')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_webiste')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_webiste')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_studios')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_studios')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_members')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_members')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_locations')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_locations')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_publishers')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_publishers')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_releases')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_releases')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_languages')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_languages')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_genres')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_genres')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_awards')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_awards')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_stores')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_stores')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_article_links')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_article_links')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_sources')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_sources')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_images')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_images')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_social_networks')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_social_networks')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_video')->willReturn($field_item_not_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_video')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_contextual_links')->willReturn($field_item_empty->reveal())->shouldBeCalled();
    $this->testGame->hasField('field_credits')->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_credits')->willReturn($field_item_empty->reveal())->shouldBeCalled();
  }

  /**
   * @covers ::calculation
   */
  public function testCalculation() {
    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(9643, $score);
  }

  /**
   * @covers ::calculation
   *
   * @dataProvider providerContextualLinks
   */
  public function testCalculationWithContextualLinks(string $type, int $expected_score) {
    $field_item_data = $this->prophesize(TypedDataInterface::class);
    $field_item_data->getValue()->willReturn(['type' => $type])->shouldBeCalled();

    $field_item = $this->prophesize(FieldItemInterface::class);

    $field_item->isEmpty()->willReturn(FALSE)->shouldBeCalled();
    $field_item->rewind()->shouldBeCalled();
    $field_item->valid()->willReturn(TRUE, FALSE)->shouldBeCalled();
    $field_item->next()->shouldBeCalled();
    $field_item->current()->willReturn($field_item_data->reveal())->shouldBeCalled();

    $this->testGame->get('field_contextual_links')->willReturn($field_item->reveal())->shouldBeCalled();

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals($expected_score, $score);
  }

  /**
   * Data provider for ::testCalculationWithContextualLinks.
   *
   * @return iterable
   *   Data provided.
   */
  public function providerContextualLinks(): iterable {
    yield ['foo', 9643];

    yield ['presskit', 9653];

    yield ['devlog', 9644];

    yield ['online_play', 10643];

    yield ['download_page', 10643];

    yield ['direct_download', 10643];

    yield ['box_art', 9743];
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithCredit() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(FALSE)->shouldBeCalled();
    $this->testGame->get('field_credits')->willReturn($field_item->reveal())->shouldBeCalled();

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(9653, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithoutImages() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_images')->willReturn($field_item->reveal())->shouldBeCalled();

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(6643, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithoutMembers() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_members')->willReturn($field_item->reveal())->shouldBeCalled();

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(8643, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithoutMembersNeitherStudios() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_studios')->willReturn($field_item->reveal())->shouldBeCalled();
    $this->testGame->get('field_members')->willReturn($field_item->reveal())->shouldBeCalled();

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(6143, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithoutReleases() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_releases')->willReturn($field_item->reveal())->shouldBeCalled();

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(7893, $score);
  }

  /**
   * @covers ::calculation
   */
  public function testCalculationWithoutStudios() {
    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->isEmpty()->willReturn(TRUE)->shouldBeCalled();
    $this->testGame->get('field_studios')->willReturn($field_item->reveal())->shouldBeCalled();

    $score = CompletenessCalculator::calculation($this->testGame->reveal());
    self::assertEquals(8643, $score);
  }

}
