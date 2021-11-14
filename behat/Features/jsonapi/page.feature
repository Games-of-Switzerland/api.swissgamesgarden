Feature: Page

  Scenario: Fetching a page with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/node/page/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a page by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/node/page/38e10164-166d-4c76-be68-0a3a48cf2966"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/node/page/18"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetch a page using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/node/page/38e10164-166d-4c76-be68-0a3a48cf2966"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.title" should be equal to "About us"
    And the JSON node "data.attributes.field_path" should be equal to "/pages/about-us"

  Scenario: Fetching page using the path filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/node/page?filter[field_path]=/pages/about-us"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.langcode" should be equal to "en"
    And the JSON node "data[0].attributes.title" should be equal to "About us"
    And the JSON node "data[0].attributes.field_path" should be equal to "/pages/about-us"
