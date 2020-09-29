<?php

namespace Drupal\gos_game;

use DateTimeImmutable;
use Drupal\node\NodeInterface;

/**
 * Class to compile Release's Platforms and Years.
 */
class ReleasesCompiler {

  /**
   * From a Game node with multiple releases, compile Platforms.
   *
   * @param \Drupal\node\NodeInterface $game
   *   The game to compile platforms.
   *
   * @return array
   *   Compiled platforms for all releases.
   */
  public static function compilePlatforms(NodeInterface $game): array {
    // Don't process node without/empty field_releases.
    if (!$game->hasField('field_releases') || $game->get('field_releases')->isEmpty()) {
      return [];
    }

    $platforms = [];

    foreach ($game->get('field_releases') as $release) {
      // Skip release without platforms.
      if (!isset($release->entity)) {
        continue;
      }

      $platform_slug = $release->entity->get('field_slug')->value;
      $platforms[$platform_slug] = ['name' => $platform_slug];
    }

    return array_keys($platforms);
  }

  /**
   * From a Game node with multiple releases, compile Platforms by Years.
   *
   * Those years will be ordered ASC.
   *
   * @param \Drupal\node\NodeInterface $game
   *   The game to compile years for.
   *
   * @throws \Exception
   *   May throw an exception of malformed date_value.
   *
   * @return array
   *   Compiled Years ordered ASC from releases.
   *   An empty array when game has no releases.
   */
  public static function compilePlatformsByYears(NodeInterface $game): array {
    // Don't process node without/empty field_releases.
    if (!$game->hasField('field_releases') || $game->get('field_releases')->isEmpty()) {
      return [];
    }

    $platforms_by_years = [];

    foreach ($game->get('field_releases') as $release) {
      // Skip release without date.
      if (!isset($release->date_value)) {
        continue;
      }

      // Get the Year from date_value release.
      $year = (new DateTimeImmutable($release->date_value))->format('Y');

      if (!isset($platforms_by_years[$year])) {
        $platforms_by_years[$year] = [
          'year' => $year,
          'platforms' => [],
        ];
      }

      // Don't process further on release without platform.
      if (!isset($release->entity)) {
        continue;
      }

      $platform_slug = $release->entity->get('field_slug')->value;
      $platforms_by_years[$year]['platforms'][$platform_slug] = ['name' => $platform_slug];
    }

    // Order by years ASC.
    ksort($platforms_by_years);

    return $platforms_by_years;
  }


}
