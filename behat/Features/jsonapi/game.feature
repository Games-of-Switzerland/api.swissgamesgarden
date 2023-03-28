@jsonapi
Feature: Game

  Scenario: Fetching a game with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/node/game/abcdef"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a game by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/node/game/1"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching an unpublished game should not be allowed.
    Given I request "/G70VW4Y9sP/jsonapi/node/game/12b7a617-4c66-4fb1-adf0-0ad70b775b9c"
    Then the response code is 403

  Scenario: Fetch a game using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "title": "Farming Simulator 18",
            "field_path": "/games/farming-simulator-18",
            "release_years": [
              "2017",
              "2018"
            ]
          }
        }
      }
      """

  Scenario: Fetching a game using the path filter may return one or more results.
    Given I request "/G70VW4Y9sP/jsonapi/node/game?filter[field_path]=/games/farming-simulator-18"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "langcode": "en",
            "title": "Farming Simulator 18",
            "field_path": "/games/farming-simulator-18"
          }
        }
      }
      """

  Scenario: Fetching a game with studio should be possible.
    Given I request "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=studios"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "included": "@arrayLength(1)"
      }
      """
    Then the response body contains JSON:
      """
      {
        "included": [
          {
            "type": "node--studio",
            "attributes": {
              "title": "Giants Software"
            }
          }
        ]
      }
      """

  Scenario: Fetching a game with members should be possible.
    Given I request "/G70VW4Y9sP/jsonapi/node/game/9bb9538f-5b75-4dc0-99b1-ff11d4e2abdd?include=members"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "included": "@arrayLength(2)"
      }
      """
    Then the response body contains JSON:
      """
      {
        "included": [
          {
            "type": "node--people",
            "attributes": {
              "title": "Jérémy \"Wuthrer\" Cuany"
            }
          },
          {
            "type": "node--people",
            "attributes": {
              "title": "Nicolas \"Kaihnn\" Jadaud"
            }
          }
        ]
      }
      """

  Scenario: Fetching a game with compiled Release Platforms should be possible.
    Given I request "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=release_platforms"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "included": "@arrayLength(6)"
      }
      """
    Then the response body contains JSON:
      """
      {
        "included": [
          {
            "type": "taxonomy_term--platform",
            "attributes": {
              "slug": "pc"
            }
          },
          {
            "type": "taxonomy_term--platform",
            "attributes": {
              "slug": "mac"
            }
          },
          {
            "type": "taxonomy_term--platform",
            "attributes": {
              "slug": "ios"
            }
          },
          {
            "type": "taxonomy_term--platform",
            "attributes": {
              "slug": "android"
            }
          },
          {
            "type": "taxonomy_term--platform",
            "attributes": {
              "slug": "playstation_vita"
            }
          },
          {
            "type": "taxonomy_term--platform",
            "attributes": {
              "slug": "nintendo_3ds"
            }
          }
        ]
      }
      """

  Scenario: Fetching a game with images return his image styles.
    Given I request "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=images"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "included": "@arrayLength(1)"
      }
      """
    Then the response body contains JSON:
      """
      {
        "included": [
          {
            "type": "file--file",
            "links": {
              "3x2_660x440": "@variableType(object)",
              "3x2_330x220": "@variableType(object)",
              "downscale_1350x1000": "@variableType(object)",
              "downscale_2560x1600": "@variableType(object)",
              "downscale_330x660": "@variableType(object)",
              "downscale_675x500": "@variableType(object)",
              "placeholder_30x30": "@variableType(object)"
            }
          }
        ]
      }
      """

  Scenario: Fetching a game using a specific Consumer ID should return only Image Styled allowed for this consumer.
    Given the "X-Consumer-ID" request header is "1df6bf5b-f58f-4870-b0b1-b0f6561efdcd"
    And the "Accept" request header is "application/vnd.api+json"
    When I request "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=images"
    Then the response body contains JSON:
      """
      {
        "included[0]":
        {
          "type": "file--file",
          "links": {
            "3x2_660x440": "@variableType(object)",
            "3x2_330x220": "@variableType(object)",
            "downscale_1350x1000": "@variableType(object)",
            "downscale_2560x1600": "@variableType(object)",
            "downscale_330x660": "@variableType(object)",
            "downscale_675x500": "@variableType(object)",
            "placeholder_30x30": "@variableType(object)"
          }
        }
      }
      """

  Scenario: Fetching a game with video return his remote video.
    Given I request "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=video"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "included": "@arrayLength(1)"
      }
      """
    Then the response body contains JSON:
    """
      {
        "included": [
          {
            "type": "media--remote_video",
            "attributes": {
              "field_media_oembed_video": "https://www.youtube.com/watch?v=y-Vgumda_mY"
            }
          }
        ]
      }
      """
