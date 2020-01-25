Feature: Persons

  Scenario: The list of people return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/node/people"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 3 elements

  Scenario: Sorting of people listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/node/people?sort=-title"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.title" should be equal to 'Nicolas "Kaihnn" Jadaud'

