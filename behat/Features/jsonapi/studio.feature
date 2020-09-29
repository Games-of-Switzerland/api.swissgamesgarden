Feature: Studio

  Scenario: Fetching a studio with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/node/studio/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a studio by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/node/studio/b7e315a4-65a1-4a34-b989-e2a5a96e1f22"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/node/studio/1"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetch a studio using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/node/studio/b7e315a4-65a1-4a34-b989-e2a5a96e1f22"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.title" should be equal to "Giants Software"
    And the JSON node "data.attributes.field_path" should be equal to "/studios/giants-software"

  Scenario: Fetching studio using the path filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/node/studio?filter[field_path]=/studios/giants-software"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.langcode" should be equal to "en"
    And the JSON node "data[0].attributes.title" should be equal to "Giants Software"
    And the JSON node "data[0].attributes.field_path" should be equal to "/studios/giants-software"
