@jsonapi
Feature: Root

  Scenario: The root endpoit with an obfuscated url works.
    Given I am on "/G70VW4Y9sP/jsonapi"
    And the response status code should be 200

  Scenario: The original root endpoit does not works.
    Given I am on "/jsonapi"
    And the response status code should be 404
