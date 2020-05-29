Feature: Language

  Scenario: Fetching a language with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/language/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a language by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/language/c748e76c-4a40-4b04-9e69-17ea602cb0b0"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/language/29"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetch a language using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/language/c748e76c-4a40-4b04-9e69-17ea602cb0b0"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.name" should be equal to "English"
