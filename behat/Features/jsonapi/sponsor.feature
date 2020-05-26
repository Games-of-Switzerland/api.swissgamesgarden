Feature: Sponsor

  Scenario: Fetching a sponsor with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a sponsor by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor/dc78845d-ba97-4fe3-889b-30680eedcd91"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor/26"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetch a sponsor using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor/dc78845d-ba97-4fe3-889b-30680eedcd91"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.name" should be equal to "Kickstarter"
    And the JSON node "data.attributes.field_path" should be equal to "/sponsors/kickstarter"

  Scenario: Fetching sponsor using the path filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor?filter[field_path]=/sponsors/kickstarter"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.langcode" should be equal to "en"
    And the JSON node "data[0].attributes.name" should be equal to "Kickstarter"
    And the JSON node "data[0].attributes.field_path" should be equal to "/sponsors/kickstarter"
