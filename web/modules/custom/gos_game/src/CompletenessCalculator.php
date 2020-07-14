<?php

namespace Drupal\gos_game;

use Drupal\node\NodeInterface;

/**
 * Class to calculate game score.
 */
class CompletenessCalculator {

  /**
   * List of fields and given absolute score given when non-empty.
   *
   * @var array
   */
  protected const FIELDS_SCORE = [
    'field_webiste' => 1000,
    'field_studios' => 1000,
    'field_members' => 1000,
    'field_locations' => 10,
    'field_publishers' => 10,
    'field_releases' => 1500,
    'field_languages' => 10,
    'field_genres' => 1,
    'field_awards' => 10,
    'field_stores' => 100,
    'field_article_links' => 1,
    'field_sources' => 1000,
    'field_images' => 2000,
    'body' => 1000,
  ];

  /**
   * Calculate the completeness score of Game.
   *
   * @param \Drupal\node\NodeInterface $game
   *   The game to calculate completeness score.
   *
   * @return float
   *   The score value. The value can be negative.
   */
  public static function calculation(NodeInterface $game): float {
    $score = 0;

    foreach (self::FIELDS_SCORE as $field => $point) {
      if ($game->hasField($field) && !$game->get($field)->isEmpty()) {
        $score += $point;
      }
    }

    // Remove many points if no members & studio are given.
    if ($game->get('field_members')->isEmpty() && $game->get('field_studios')->isEmpty()) {
      $score -= 1500;
    }

    // Remove many points if not images are given.
    if ($game->get('field_images')->isEmpty()) {
      $score -= 1000;
    }

    // Remove many points if no releases/platforms are given.
    if ($game->get('field_releases')->isEmpty()) {
      $score -= 250;
    }

    return $score;
  }

}
