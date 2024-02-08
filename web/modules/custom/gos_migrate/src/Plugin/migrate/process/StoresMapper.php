<?php

namespace Drupal\gos_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts a single Store link into an compliant single value or array values.
 *
 * Available configuration keys
 * - get: The stores mapper values to get.
 * Possible values:
 *   - store: The store key name.
 *   - link: The store source link.
 *
 * Examples:
 *
 * @code
 * process:
 *   field_stores:
 *     plugin: gos_stores_mapper
 *     get: link
 *     source: field_stores
 *
 * @endcode
 *
 * If the source value was
 * 'https://itunes.apple.com/us/app/atomarium/id496040059'
 * the transformed value would be ['store' => 'apple_store',
 * 'link' => 'https://itunes.apple.com/us/app/atomarium/id496040059].
 *
 * @see Drupal\gos_site\Plugin\Field\FieldType\StoreItem
 *
 * @MigrateProcessPlugin(
 *     id="gos_stores_mapper"
 * )
 */
class StoresMapper extends ProcessPluginBase {

  /**
   * Collection of Stores Key (compatible with StoreItem) and matchable URLs.
   *
   * @string[]
   */
  private const STORES = [
    'apple_store' => [
      '^(http|https)://itunes.apple.com/.*',
      '^(http|https)://apps.apple.com/.*',
      '^(http|https)://itunes.com/app/.*',
      '^(http|https)://geo.itunes.apple.com/.*',
      '^(http|https)://apple.co/.*',
    ],
    'amazon' => [
      '^(http|https)://www.amazon.com/.*',
      '^(http|https)://www.amazon.com.*/.*',
    ],
    'facebook' => [
      '^(http|https)://apps.facebook.com/.*',
    ],
    'steam' => [
      '^(http|https)://store.steampowered.com/.*',
    ],
    'itchio' => [
      '^(http|https)://itch.io/.*',
      '^(http|https)://.*.itch.io/.*',
    ],
    'xbox' => [
      '^(http|https)://marketplace.xbox.com/.*',
    ],
    'playstation' => [
      '^(http|https)://store.playstation.com/.*',
    ],
    'gog' => [
      '^(http|https)://www.gog.com/game/.*',
    ],
    'nintendo' => [
      '^(http|https)://www.nintendo.com/.*',
    ],
    'microsoft_store' => [
      '^(http|https)://www.microsoft.com/.*',
      '^(http|https)://www.windowsphone.com/.*',
      '^(http|https)://apps.microsoft.com/windows/app/.*',
    ],
    'oculus' => [
      '^(http|https)://www.oculus.com/.*',
    ],
    'google_play_store' => [
      '^(http|https)://play.google.com/.*',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $get_keyname = (isset($this->configuration['get']) && \is_string($this->configuration['get']) && $this->configuration['get'] !== '') ? $this->configuration['get'] : NULL;

    if (empty($value) && $value !== '0' && $value !== 0) {
      throw new MigrateSkipProcessException('The store link name should not be empty.');
    }

    if (!filter_var($value, \FILTER_VALIDATE_URL)) {
      throw new MigrateSkipProcessException(sprintf('The store link %s is not a valid URL.', $value));
    }

    // Validate the configuration.
    if (empty($this->configuration['get'])) {
      throw new MigrateException('Formats date plugin is missing from_formats configuration.');
    }

    $value = (string) $value;
    $store_structure = $this->lookupMappedStore($value);

    if ($get_keyname && isset($store_structure[$get_keyname])) {
      return $store_structure[$get_keyname];
    }

    return $store_structure;
  }

  /**
   * Lookup for a Store based on a per-game Store URL.
   *
   * @param string $url
   *   The Store URL to map with an existing store.
   *
   * @return array
   *   the mapped structure with store info or a custom structure.
   */
  private function lookupMappedStore(string $url): array {
    foreach (self::STORES as $key => $store) {
      foreach ($store as $regex) {
        $matches = [];

        if (!preg_match(";{$regex};", $url, $matches)) {
          continue;
        }

        return [
          'store' => $key,
          'link' => $url,
        ];
      }
    }

    return [
      'store' => 'custom',
      'link' => $url,
    ];
  }

}
