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

  Scenario: The default language when fetching a game is English.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63"
    And the JSON node "data.attributes.langcode" should be equal to "en"
    Given I am on "/G70VW4Y9sP/jsonapi/node/game?filter[field_path]=/games/farming-simulator-18"
    And the JSON node "data[0].attributes.langcode" should be equal to "en"

  Scenario: The first game has the correct title.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data.attributes.title" should be equal to "Farming Simulator 18"

  Scenario: Fetching games using the path filter may return one or more results.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game?filter[field_path]=/games/farming-simulator-18"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.title" should be equal to "Farming Simulator 18"

#  Scenario: Fetching a game should return his image styles on image field(s).
#    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=avatar"
#    Then the response status code should be 200
#    And the response should be in JSON
#    And the JSON node "included[0].type" should exist
#    And the JSON node "included[0].links.large.href" should exist
#    And the JSON node "included[0].links.medium.href" should exist
#    And the JSON node "included[0].links.thumbnail.href" should exist

#  Scenario: Getting the cover images return the urls and image styles
#    Given the "X-Consumer-ID" request header is "5eacaa54-e45f-4bd2-b3e6-e720b217034b	"
#    And the "Accept" request header is "application/vnd.api+json"
#    When I request "/jDYJitaKOq/jsonapi/node/property/6fecc24e-ad37-400b-bfc0-51247c7449b6?include=cover_images,cover_images.image"
#    # TODO Change image styles names when created
#    Then the response body contains JSON:
#      """
#      {
#        "included[0]":
#        {
#          "type": "media--image"
#        },
#        "included[3]":
#        {
#          "type": "file--file",
#          "links":
#          {
#            "large": "@variableType(object)"
#          }
#        }
#      }
#      """
