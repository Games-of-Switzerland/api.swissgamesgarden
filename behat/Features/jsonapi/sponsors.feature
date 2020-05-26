Feature: Sponsors

  Scenario: The list of sponsor return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 3 elements
    And the JSON node "data[0].attributes.name" should be equal to "Kickstarter"
    And the JSON node "data[1].attributes.name" should be equal to "Numerik Games Festival"
    And the JSON node "data[2].attributes.name" should be equal to "Alimentarium"

  Scenario: Sorting of sponsor listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor?sort=name"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.name" should be equal to "Alimentarium"
    And the JSON node "data[1].attributes.name" should be equal to "Kickstarter"
    And the JSON node "data[2].attributes.name" should be equal to "Numerik Games Festival"
