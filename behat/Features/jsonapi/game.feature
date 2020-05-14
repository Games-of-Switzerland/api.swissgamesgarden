Feature: Game

  Scenario: Fetching a game with a wrong UUID should return a 404.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/abcdef"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching a game by his UUID works, fetching it by his NID does not works.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63"
    Then the response status code should be 200
    And the response should be in JSON
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/1"
    Then the response status code should be 404
    And the response should be in JSON

  Scenario: Fetching an unpublished game should not be allowed.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/12b7a617-4c66-4fb1-adf0-0ad70b775b9c"
    Then the response status code should be 403

  Scenario: Fetch a game using UUID should return it in the default language
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data.attributes.langcode" should be equal to "en"
    And the JSON node "data.attributes.title" should be equal to "Farming Simulator 18"
    And the JSON node "data.attributes.field_path" should be equal to "/games/farming-simulator-18"

  Scenario: Fetching a game using the path filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game?filter[field_path]=/games/farming-simulator-18"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.langcode" should be equal to "en"
    And the JSON node "data[0].attributes.title" should be equal to "Farming Simulator 18"
    And the JSON node "data[0].attributes.field_path" should be equal to "/games/farming-simulator-18"

  Scenario: Fetching a game with studio should be possible.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=studios"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "included" should have 1 element
    And the JSON node "included[0].type" should be equal to "node--studio"
    And the JSON node "included[0].attributes.title" should be equal to "Giants Software"

  Scenario: Fetching a game with members should be possible.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/9bb9538f-5b75-4dc0-99b1-ff11d4e2abdd?include=members"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "included" should have 2 elements
    And the JSON node "included[0].type" should be equal to "node--people"
    And the JSON node "included[0].attributes.title" should be equal to 'Jérémy "Wuthrer" Cuany'
    And the JSON node "included[1].type" should be equal to "node--people"
    And the JSON node "included[1].attributes.title" should be equal to 'Nicolas "Kaihnn" Jadaud'

  Scenario: Fetching a game with images return his image styles.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=images"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "included" should have 1 element
    And the JSON node "included[0].type" should be equal to "file--file"
    And the JSON node "included[0].links.large.href" should exist
    And the JSON node "included[0].links.medium.href" should exist
    And the JSON node "included[0].links.thumbnail.href" should exist

  Scenario: Fetching a game using a specific Consumer ID should return only Image Styled allowd for this consumer.
    Given the "X-Consumer-ID" request header is "1df6bf5b-f58f-4870-b0b1-b0f6561efdcd"
    And the "Accept" request header is "application/vnd.api+json"
    When I request "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=images"
    Then the response body contains JSON:
      """
      {
        "included[0]":
        {
          "type": "file--file",
          "links":
          {
            "medium": "@variableType(object)",
            "large": "@variableType(object)",
            "thumbnail": "@variableType(object)"
          }
        }
      }
      """
