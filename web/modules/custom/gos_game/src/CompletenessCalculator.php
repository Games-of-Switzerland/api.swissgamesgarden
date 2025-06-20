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
  protected const array FIELDS_SCORE = [
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
    'field_video' => 1000,
    'body' => 1000,
    'field_social_networks' => 1,
    'field_credits' => 10,
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

    // Add Points according contextual links.
    if (!$game->get('field_contextual_links')->isEmpty()) {
      foreach ($game->get('field_contextual_links') as $link) {
        switch ($link->getValue()['type']) {
          case 'presskit':
            $score += 10;

            break;

          case 'devlog':
            ++$score;

            break;

          case 'online_play':
          case 'download_page':
          case 'direct_download':
            $score += 1000;

            break;

          case 'box_art':
            $score += 100;

            break;
        }
      }
    }

    return $score;
  }

}
