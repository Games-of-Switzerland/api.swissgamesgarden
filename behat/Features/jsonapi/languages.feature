Feature: Languages

  Scenario: The list of language return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/language"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 4 elements
    And the JSON node "data[0].attributes.name" should be equal to "English"
    And the JSON node "data[1].attributes.name" should be equal to "French"
    And the JSON node "data[2].attributes.name" should be equal to "German"
    And the JSON node "data[3].attributes.name" should be equal to "Spanish"

  Scenario: Sorting of language listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/taxonomy_term/language?sort=-name"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.name" should be equal to "Spanish"
    And the JSON node "data[1].attributes.name" should be equal to "German"
    And the JSON node "data[2].attributes.name" should be equal to "French"
    And the JSON node "data[3].attributes.name" should be equal to "English"
