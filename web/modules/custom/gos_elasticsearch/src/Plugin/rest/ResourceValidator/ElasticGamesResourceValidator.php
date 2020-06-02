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
  ];

  /**
   * The game Genres to filter by.
   *
   * This field uses the custom validation ::validateGenres.
   *
   * @var \Drupal\taxonomy\TermInterface[]|null
   */
  private $genres;

  /**
   * The Genres Uuid.
   *
   * @var string[]
   */
  private $genresUuid;

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
   * The Platforms Uuid.
   *
   * @var string[]
   */
  private $platformsUuid;

  /**
   * Sort property with direction as key.
   *
   * This property uses the custom validation ::validateSort.
   *
   * @var array
   */
  private $sort = [];

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
   * Get the game Genres UUID to filter by.
   *
   * @return string[]|null
   *   Genres uuid.
   */
  public function getGenresUuid(): ?array {
    return $this->genresUuid;
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
   * Get the game Platforms UUID to filter by.
   *
   * @return string[]|null
   *   Platforms uuid.
   */
  public function getPlatformsUuid(): ?array {
    return $this->platformsUuid;
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
   * Set the Genres to filter by.
   *
   * @param \Drupal\taxonomy\TermInterface[] $genres
   *   Genres to filter by.
   */
  public function setGenres(array $genres): void {
    $this->genres = $genres;
  }

  /**
   * Set the Genres Uuid.
   *
   * @param string[] $genres_uuid
   *   Genres Uuid.
   */
  public function setGenresUuid(array $genres_uuid): void {
    $this->genresUuid = $genres_uuid;
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
   * Set the Platforms Uuid.
   *
   * @param string[] $platforms_uuid
   *   Platforms Uuid.
   */
  public function setPlatformsUuid(array $platforms_uuid): void {
    $this->platformsUuid = $platforms_uuid;
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
    // @TODO Improve error detection by checking both array dimensions and give
    // proper UUID feedback error.
    if ($this->genresUuid && !$this->genres) {
      $context->buildViolation(sprintf('At least one given Genre(s) UUID has not been found.'))
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
    // @TODO Improve error detection by checking both array dimensions and give
    // proper UUID feedback error.
    if ($this->platformsUuid && !$this->platforms) {
      $context->buildViolation(sprintf('At least one given Platform(s) UUID has not been found.'))
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
