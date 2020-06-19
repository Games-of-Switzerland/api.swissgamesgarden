Feature: Location

  Scenario: Fetching a location with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/location/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a location by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/location/e181208c-fd4d-47bc-9767-4965d670bc2f"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/location/34"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetch a location using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/location/e181208c-fd4d-47bc-9767-4965d670bc2f"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.name" should be equal to "Zürich"
    And the JSON node "data.attributes.slug" should be equal to "zurich"

  Scenario: Fetching location using the slug filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/location?filter[slug]=zurich"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.langcode" should be equal to "en"
    And the JSON node "data[0].attributes.name" should be equal to "Zürich"
    And the JSON node "data[0].attributes.slug" should be equal to "zurich"
