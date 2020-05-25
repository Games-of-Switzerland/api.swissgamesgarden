Feature: Publisher

  Scenario: Fetching a publisher with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a publisher by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher/c16c1119-ae04-460c-9fed-e9e20d5785e7"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher/23"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetch a publisher using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher/c16c1119-ae04-460c-9fed-e9e20d5785e7"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.title" should be equal to "Shy Robot Games"
    And the JSON node "data.attributes.field_path" should be equal to "/publishers/shy-robot-games"

  Scenario: Fetching publisher using the path filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher?filter[field_path]=/publishers/shy-robot-games"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.title" should be equal to "Shy Robot Games"
    And the JSON node "data.attributes.field_path" should be equal to "/publishers/shy-robot-games"
