<?php

namespace Drupal\gos_rest\Plugin\rest\ResourceValidator;

/**
 * Base Serializer for ArrayAccess.
 *
 * @template-implements \ArrayAccess<string, mixed>
 */
abstract class BaseValidator implements \ArrayAccess {

  /**
   * Initialize a new BaseValidator object.
   *
   * @param array $data
   *   Sanitized data.
   */
  public function __construct(array $data = []) {
    foreach ($data as $key => $value) {
      $this->offsetSet($key, $value);
    }
  }

  /**
   * Whether or not an offset exists.
   *
   * @param mixed $offset
   *   An offset to check for.
   *
   * @return bool
   *   Does this offset exists.
   */
  public function offsetExists(mixed $offset): bool {
    return property_exists(static::class, $offset);
  }

  /**
   * Returns the value at specified offset.
   *
   * @param mixed $offset
   *   The offset to retrieve.
   *
   * @return mixed
   *   The value to get from given offset.
   */
  public function offsetGet(mixed $offset): mixed {
    $offset = $this->camelize($offset);

    if (!$this->offsetExists($offset)) {
      throw new \InvalidArgumentException(sprintf('Unsupported offset %s.', $offset));
    }

    $getter = 'get' . ucfirst($offset);

    return $this->$getter();
  }

  /**
   * Assigns a value to the specified offset.
   *
   * @param mixed $offset
   *   The offset to assign the value to.
   * @param mixed $value
   *   The value to set.
   */
  public function offsetSet(mixed $offset, mixed $value): void {
    $offset = $this->camelize($offset);

    if (!$this->offsetExists($offset)) {
      throw new \InvalidArgumentException(sprintf('Unsupported offset %s.', $offset));
    }

    // Prevent saving of null value.
    if ($value === NULL) {
      return;
    }

    $setter = 'set' . ucfirst($offset);
    $this->$setter($value);
  }

  /**
   * Unsets an offset.
   *
   * @param mixed $offset
   *   The offset to unset.
   */
  public function offsetUnset(mixed $offset): void {
    throw new \BadMethodCallException('Unsupported method.');
  }

  /**
   * Transform a given snake_case input into lowerCamelCase form.
   *
   * @param string $input
   *   The input to be transformed.
   * @param string $separator
   *   The snake_case separator.
   *
   * @return string
   *   The lowerCamelCase value of given input.
   */
  private function camelize(string $input, string $separator = '_'): string {
    return str_replace($separator, '', lcfirst(ucwords($input, $separator)));
  }

}
