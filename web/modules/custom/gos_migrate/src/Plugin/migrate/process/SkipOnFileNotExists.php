<?php

namespace Drupal\gos_migrate\Plugin\migrate\process;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Skips processing the current row when the input file url is not exist.
 *
 * Available configuration keys:
 * - method: (optional) What to do if the input file uri does not exist.
 *   - row: Skips the entire row.
 *   - process: Prevents further processing of the input property.
 * - message: (optional) A message to be logged in the {migrate_message_*} table
 *   for this row. Messages are only logged for the 'row' method. If not set,
 *   nothing is logged in the message table.
 *
 * Examples:
 *
 * @code
 * process:
 *   file:
 *     plugin: skip_on_file_not_exists
 *     method: row
 *     source: fileurl
 *     message: 'File field_name does not exist'
 * @endcode
 * The above example will skip processing any row
 * if file 'fileurl' does not exist
 * and log the message in the message table.
 *
 * @MigrateProcessPlugin(
 *     id="skip_on_file_not_exists"
 * )
 */
class SkipOnFileNotExists extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The Guzzle HTTP Client service.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress MissingParamType
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   *
   * @psalm-suppress PossiblyNullArgument
   * @psalm-suppress ArgumentTypeCoercion
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * Stops processing the current property when value is set.
   *
   * @param mixed $value
   *   The input value.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migration in which this process is being executed.
   * @param \Drupal\migrate\Row $row
   *   The row from the source to process.
   * @param string $destination_property
   *   The destination property currently worked on. This is only used together
   *   with the $row above.
   *
   * @throws \Drupal\migrate\MigrateSkipProcessException
   *   Thrown if the source property is not set and rest of the process should
   *   be skipped.
   *
   * @return mixed
   *   The input value, $value, if it is not empty.
   */
  public function process($value, MigrateExecutableInterface $migrate_executable, Row $row, string $destination_property) {
    if (!$this->checkFile($value)) {
      throw new MigrateSkipProcessException();
    }

    return $value;
  }

  /**
   * Skips the current row when value is set.
   *
   * @param mixed $value
   *   The input value.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migration in which this process is being executed.
   * @param \Drupal\migrate\Row $row
   *   The row from the source to process.
   * @param string $destination_property
   *   The destination property currently worked on. This is only used together
   *   with the $row above.
   *
   * @throws \Drupal\migrate\MigrateSkipRowException
   *   Thrown if the source property is not set and the row should be skipped,
   *   records with STATUS_IGNORED status in the map.
   *
   * @return mixed
   *   The input value, $value, if it is not empty.
   */
  public function row($value, MigrateExecutableInterface $migrate_executable, Row $row, string $destination_property) {
    $message = !empty($this->configuration['message']) ? $this->configuration['message'] : '';

    if (!$this->checkFile($value)) {
      throw new MigrateSkipRowException($message);
    }

    return $value;
  }

  /**
   * Check if file (remote or local) exists.
   *
   * @param mixed $value
   *   File URL.
   *
   * @return bool
   *   True if the compare successfully, FALSE otherwise.
   */
  protected function checkFile($value) {
    if (UrlHelper::isExternal($value)) {
      try {
        // Check if remote file exists.
        $this->httpClient->head($value);
      }
      catch (RequestException $e) {
        return FALSE;
      }
    }
    // Check if local file exists.
    elseif (!file_exists($value)) {
      return FALSE;
    }

    return TRUE;
  }

}
