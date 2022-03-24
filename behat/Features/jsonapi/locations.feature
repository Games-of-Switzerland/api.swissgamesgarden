Feature: Locations

  Scenario: The list of location return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/location"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 4 elements
    And the JSON node "data[0].attributes.name" should be equal to "Geneva"
    And the JSON node "data[1].attributes.name" should be equal to "Zürich"
    And the JSON node "data[2].attributes.name" should be equal to "Fribourg"
    And the JSON node "data[3].attributes.name" should be equal to "Lausanne"

  Scenario: Sorting of location listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/location?sort=name"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.name" should be equal to "Fribourg"
    And the JSON node "data[1].attributes.name" should be equal to "Geneva"
    And the JSON node "data[2].attributes.name" should be equal to "Lausanne"
    And the JSON node "data[3].attributes.name" should be equal to "Zürich"
