<?php

namespace Drupal\gos_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Concat multiple fields of single link into a multi Contextual Link(s) field.
 *
 * Available configuration keys
 * - sources: The links mapper values to get.
 * Possible values:
 *   - presskit: The Presskit link
 *   - online_play: The Online Play link
 *   - download_page: The Download Page link
 *   - direct_download: The Direct DL link
 *   - devlog: The Developer Logs link
 *   - box_art: The Art link.
 *
 * Examples:
 *
 * @code
 * process:
 *   field_links:
 *     plugin: gos_contextual_links_mapper
 *     sources:
 *       - devlog: field_devlog_link
 *       - download_page: field_download_link
 *
 * @endcode
 *
 * @see Drupal\gos_site\Plugin\Field\FieldType\StoreItem
 *
 * @MigrateProcessPlugin(
 *     id="gos_contextual_links_mapper"
 * )
 */
class ContextualLinksMapper extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\migrate\MigrateSkipProcessException
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!isset($this->configuration['sources']) || empty($this->configuration['sources'])) {
      throw new MigrateSkipProcessException('The sources mapping values should not be empty.');
    }

    $links_structure = [];

    foreach ($this->configuration['sources'] as $key => $field) {
      if (!$row->hasSourceProperty($field)) {
        throw new MigrateSkipProcessException(sprintf('The "%s" source property not found.', $field));
      }

      $value = $row->getSourceProperty($field);

      if (empty($value)) {
        continue;
      }

      $links_structure[] = [
        'type' => $key,
        'url' => $value,
      ];
    }

    return $links_structure;
  }

}
