<?php

namespace Drupal\gos_elasticsearch\Plugin\rest\ResourceValidator;

// phpcs:disable
// PHPCS does not detect \Assert as use in this file and will remove it. Which
// leads to not working Annotation below.

use Drupal\gos_rest\Plugin\rest\ResourceValidator\BaseValidator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

// phpcs:enable

/**
 * Serializer class for REST GET parameters of ElasticGamesResource.
 *
 * @see \Drupal\gos_elasticsearch\Plugin\rest\resource\ElasticRealEstateListResource
 */
class ElasticGamesResourceValidator extends BaseValidator {

  /**
   * List of sortable properties.
   *
   * @var array
   */
  private const SORTABLE = [
    '_score',
    'title.keyword',
    'releases.date',
    'changed',
  ];

  /**
   * All raw element.
   *
   * This field may used in custom validators.
   *
   * @var array
   */
  protected $raw;

  /**
   * The game Genres to filter by.
   *
   * This field uses the custom validation ::validateGenres.
   *
   * @var \Drupal\taxonomy\TermInterface[]|null
   */
  private $genres;

  /**
   * The page to fetch.
   *
   * The page parameter is mandatory to avoid search overload.
   *
   * @var int
   *
   * @Assert\NotNull
   */
  private $page;

  /**
   * The game Platforms to filter by.
   *
   * This field uses the custom validation ::validatePlatforms.
   *
   * @var \Drupal\taxonomy\TermInterface[]|null
   */
  private $platforms;

  /**
   * Sort property with direction as key.
   *
   * This property uses the custom validation ::validateSort.
   *
   * @var array
   */
  private $sort = [];

  /**
   * The game Stores key to filter by.
   *
   * @var string[]|null
   *
   * @Assert\Choice(choices={"", "apple_store", "steam", "amazon", "itchio", "facebook", "epic", "playstation", "xbox", "nintendo", "microsoft_store", "oculus", "google_play_store", "gog", "other"}, multiple=true)
   */
  private $stores;

  /**
   * Get the game Genres to filter by.
   *
   * @return \Drupal\taxonomy\TermInterface[]|null
   *   Genres to filter by.
   */
  public function getGenres(): ?array {
    return $this->genres;
  }

  /**
   * Get page.
   *
   * @return int|null
   *   The value.
   */
  public function getPage(): ?int {
    return (int) $this->page;
  }

  /**
   * Get the game Platforms to filter by.
   *
   * @return \Drupal\taxonomy\TermInterface[]|null
   *   Platforms to filter by.
   */
  public function getPlatforms(): ?array {
    return $this->platforms;
  }

  /**
   * Get the raw values.
   *
   * @return array
   *   The raw values.
   */
  public function getRaw(): array {
    return $this->raw;
  }

  /**
   * Get the sort information.
   *
   * @return array
   *   Sort property with a direction (asc/desc) as key.
   */
  public function getSort(): array {
    return $this->sort;
  }

  /**
   * Get the game Stores to filter by.
   *
   * @return string[]|null
   *   Stores to filter by.
   */
  public function getStores(): ?array {
    return $this->stores;
  }

  /**
   * Set the Genres to filter by.
   *
   * @param \Drupal\taxonomy\TermInterface[] $genres
   *   Genres to filter by.
   */
  public function setGenres(array $genres): void {
    $this->genres = $genres;
  }

  /**
   * Set page name.
   *
   * @param int $page
   *   The integer page to set.
   */
  public function setPage($page): void {
    $this->page = $page;
  }

  /**
   * Set the Platforms to filter by.
   *
   * @param \Drupal\taxonomy\TermInterface[] $platforms
   *   Platforms to filter by.
   */
  public function setPlatforms(array $platforms): void {
    $this->platforms = $platforms;
  }

  /**
   * Set raw values.
   *
   * @param array $raw
   *   The raw values .
   */
  public function setRaw(array $raw): void {
    $this->raw = $raw;
  }

  /**
   * Set the sort information.
   *
   * @param array $sort
   *   Sort information.
   */
  public function setSort(array $sort): void {
    $this->sort = $sort;
  }

  /**
   * Set the Stores to filter by.
   *
   * @param string[] $stores
   *   Stores to filter by.
   */
  public function setStores(array $stores): void {
    $this->stores = $stores;
  }

  /**
   * Validates the Genres parameter.
   *
   * Ensure the given taxonomy is a proper Genres entity.
   *
   * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
   *   The validation execution context.
   * @param string $payload
   *   The Payload.
   *
   * @Assert\Callback
   */
  public function validateGenres(ExecutionContextInterface $context, $payload): void {
    if (isset($this->raw['genres']) && !$this->genres) {
      $context->buildViolation(sprintf('At least one given Genre(s) has not been found.'))
        ->atPath('genres')
        ->addViolation();
    }
  }

  /**
   * Validates the Platforms parameter.
   *
   * Ensure the given taxonomy is a proper Platforms entity.
   *
   * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
   *   The validation execution context.
   * @param string $payload
   *   The Payload.
   *
   * @Assert\Callback
   */
  public function validatePlatforms(ExecutionContextInterface $context, $payload): void {
    if (isset($this->raw['platforms']) && !$this->platforms) {
      $context->buildViolation(sprintf('At least one given Platform(s) has not been found.'))
        ->atPath('platforms')
        ->addViolation();
    }
  }

  /**
   * Validates the sort parameter.
   *
   * The sort parameter only works for the list scheme.
   *
   * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
   *   The validation execution context.
   * @param string $payload
   *   The Payload.
   *
   * @Assert\Callback
   */
  public function validateSort(ExecutionContextInterface $context, $payload): void {
    if (!$this->sort) {
      $this->sort = [];

      return;
    }

    if (!\array_key_exists('asc', $this->sort) && !\array_key_exists('desc', $this->sort)) {
      $context->buildViolation(sprintf('Provided direction "%s" is not supported. Please use "asc" or "desc".', key($this->sort)))
        ->atPath('sort')
        ->addViolation();
    }

    if (!\in_array($this->sort[key($this->sort)], $this::SORTABLE, TRUE)) {
      $context->buildViolation(sprintf('Provided property "%s" is not sortable.', $this->sort[key($this->sort)]))
        ->atPath('sort')
        ->addViolation();
    }
  }

}
