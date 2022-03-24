Feature: Location

  Scenario: Fetching a canton with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/canton/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a canton by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/canton/7eb54de6-4f2c-471e-b84b-ac7a9c8ff729"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/canton/37"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetch a canton using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/canton/7eb54de6-4f2c-471e-b84b-ac7a9c8ff729"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.name" should be equal to "Vaud"
    And the JSON node "data.attributes.slug" should be equal to "vaud"

  Scenario: Fetching canton using the slug filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/canton?filter[slug]=vaud"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.langcode" should be equal to "en"
    And the JSON node "data[0].attributes.name" should be equal to "Vaud"
    And the JSON node "data[0].attributes.slug" should be equal to "vaud"
