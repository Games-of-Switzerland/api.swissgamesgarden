Feature: Cantons

  Scenario: The list of canton return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/canton"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 2 elements
    And the JSON node "data[0].attributes.name" should be equal to "Vaud"
    And the JSON node "data[1].attributes.name" should be equal to "Geneva"

  Scenario: Sorting of canton listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/canton?sort=name"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.name" should be equal to "Geneva"
    And the JSON node "data[1].attributes.name" should be equal to "Vaud"
