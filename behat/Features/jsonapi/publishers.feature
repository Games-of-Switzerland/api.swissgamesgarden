Feature: Publishers

  Scenario: The list of publisher return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 3 elements
    And the JSON node "data[0].attributes.name" should be equal to "Shy Robot Games"
    And the JSON node "data[1].attributes.name" should be equal to "Astragon"
    And the JSON node "data[2].attributes.name" should be equal to "Focus Home Interactive"

  Scenario: Sorting of publisher listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher?sort=name"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.name" should be equal to "Astragon"
    And the JSON node "data[1].attributes.name" should be equal to "Focus Home Interactive"
    And the JSON node "data[2].attributes.name" should be equal to "Shy Robot Games"
