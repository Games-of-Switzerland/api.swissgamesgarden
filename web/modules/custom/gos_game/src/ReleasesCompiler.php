<?php

namespace Drupal\gos_game;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
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
      $platforms[$release->target_id] = [
        'tid' => $release->target_id,
        'name' => $platform_slug,
      ];
    }

    return $platforms;
  }

  /**
   * From a Game node with multiple releases, compile Years.
   *
   * Those years will be ordered ASC.
   *
   * @param \Drupal\node\NodeInterface $game
   *   The game to compile years.
   *
   * @return array
   *   Compiled years for all releases.
   */
  public static function compileYears(NodeInterface $game): array {
    // Don't process node without/empty field_releases.
    if (!$game->hasField('field_releases') || $game->get('field_releases')->isEmpty()) {
      return [];
    }

    $years = [];

    foreach ($game->get('field_releases') as $release) {
      // Skip release without date.
      if (!isset($release->date_value)) {
        continue;
      }

      $year = (new \DateTimeImmutable($release->date_value))->format('Y');
      $years[$year] = $year;
    }

    // Order by years ASC.
    ksort($years);

    return array_keys($years);
  }

  /**
   * From a Game node with multiple releases, compile Years by Platforms.
   *
   * Those years will be ordered ASC.
   *
   * @param \Drupal\node\NodeInterface $game
   *   The game to compile platforms for.
   *
   * @throws \Exception
   *   May throw an exception of malformed date_value.
   *
   * @return array
   *   Compiled Years ordered ASC from releases.
   *   An empty array when game has no platforms.
   */
  public static function compileYearsByPlatforms(NodeInterface $game): array {
    // Don't process node without/empty field_releases.
    if (!$game->hasField('field_releases') || $game->get('field_releases')->isEmpty()) {
      return [];
    }

    $years_by_platforms = [];

    foreach ($game->get('field_releases') as $release) {
      // Don't process further on release without platform.
      if (!isset($release->entity)) {
        continue;
      }

      $platform_slug = $release->entity->get('field_slug')->value;

      if (!isset($years_by_platforms[$platform_slug])) {
        $years_by_platforms[$platform_slug] = [
          'platform' => $platform_slug,
          'years' => [],
        ];
      }

      // Skip release without date.
      if (!isset($release->date_value)) {
        continue;
      }

      // Get the Year from date_value release.
      $year = (new \DateTimeImmutable($release->date_value))->format('Y');
      $years_by_platforms[$platform_slug]['years'][$year] = $year;

      // Resort the years by platform to have ordered Years ASC.
      ksort($years_by_platforms[$platform_slug]['years']);
    }

    return $years_by_platforms;
  }

  /**
   * From Game with multiple releases, compile Platforms by Years w/ states.
   *
   * Those years will be ordered ASC.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $game
   *   The game to compile years for.
   *
   * @throws \Exception
   *   May throw an exception of malformed date_value.
   *
   * @return array
   *   Compiled Years ordered ASC from releases.
   *   An empty array when game has no releases.
   */
  public static function normalizeReleases(ContentEntityInterface $game): array {
    // Don't process node without/empty field_releases.
    if (!$game->hasField('field_releases') || $game->get('field_releases')->isEmpty()) {
      return [];
    }

    $normalized = [];

    foreach ($game->get('field_releases') as $release) {
      $year = 'na';

      $date = $release->date_value ? (new \DateTimeImmutable($release->date_value)) : NULL;

      // Get the Year from date_value release.
      if ($date instanceof \DateTimeImmutable) {
        $year = (new \DateTimeImmutable($release->date_value))->format('Y');
      }

      if (!isset($normalized[$year])) {
        $normalized[$year] = [
          'year' => $release->date_value ? $year : NULL,
          'platforms' => [],
          'states' => [],
        ];
      }

      // Don't process further on release without state.
      if (isset($release->state)) {
        $normalized[$year]['states'][$release->state] = $release->state;
      }

      // Don't process further on release without platform.
      if (isset($release->entity)) {
        $platform_slug = $release->entity->get('field_slug')->value;
        $normalized[$year]['platforms'][$release->target_id] = [
          'name' => $platform_slug,
          'tid' => $release->target_id,
          'date' => $date instanceof \DateTimeImmutable ? $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT) : NULL,
          'state' => $release->state ?? NULL,
        ];
      }
    }

    // Order by years ASC.
    ksort($normalized);

    return $normalized;
  }

}
