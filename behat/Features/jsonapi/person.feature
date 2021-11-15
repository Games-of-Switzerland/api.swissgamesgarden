Feature: Person

  Scenario: Fetching a person with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/node/people/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a person by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/node/people/c2c1d560-d9f4-4878-8105-7f63ec09e7ef"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/node/people/1"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetch a person using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/node/people/c2c1d560-d9f4-4878-8105-7f63ec09e7ef"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.title" should be equal to 'Jérémy "Wuthrer" Cuany'
    And the JSON node "data.attributes.field_path" should be equal to '/people/jeremy-wuthrer-cuany'

  Scenario: Fetching people using the path filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/node/people?filter[field_path]=/people/jeremy-wuthrer-cuany"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.langcode" should be equal to "en"
    And the JSON node "data[0].attributes.title" should be equal to 'Jérémy "Wuthrer" Cuany'
    And the JSON node "data[0].attributes.field_path" should be equal to '/people/jeremy-wuthrer-cuany'
