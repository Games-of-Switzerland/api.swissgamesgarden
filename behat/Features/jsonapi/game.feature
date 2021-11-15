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
    And the JSON node "data.attributes.release_years" should have 2 elements
    And the JSON node "data.attributes.release_years[0]" should be equal to "2017"
    And the JSON node "data.attributes.release_years[1]" should be equal to "2018"

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

  Scenario: Fetching a game with compiled Release Platforms should be possible.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=release_platforms"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "included" should have 6 elements
    And the JSON node "included[0].type" should be equal to "taxonomy_term--platform"
    And the JSON node "included[0].attributes.slug" should be equal to 'pc'
    And the JSON node "included[1].type" should be equal to "taxonomy_term--platform"
    And the JSON node "included[1].attributes.slug" should be equal to 'mac'
    And the JSON node "included[2].type" should be equal to "taxonomy_term--platform"
    And the JSON node "included[2].attributes.slug" should be equal to 'ios'
    And the JSON node "included[3].type" should be equal to "taxonomy_term--platform"
    And the JSON node "included[3].attributes.slug" should be equal to 'android'
    And the JSON node "included[4].type" should be equal to "taxonomy_term--platform"
    And the JSON node "included[4].attributes.slug" should be equal to 'playstation_vita'
    And the JSON node "included[5].type" should be equal to "taxonomy_term--platform"
    And the JSON node "included[5].attributes.slug" should be equal to 'nintendo_3ds'

  Scenario: Fetching a game with images return his image styles.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=images"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "included" should have 1 element
    And the JSON node "included[0].type" should be equal to "file--file"
    And the JSON node "included[0].links.3x2_330x220.href" should exist
    And the JSON node "included[0].links.3x2_660x440.href" should exist
    And the JSON node "included[0].links.downscale_1350x1000.href" should exist
    And the JSON node "included[0].links.downscale_2560x1600.href" should exist
    And the JSON node "included[0].links.downscale_330x660.href" should exist
    And the JSON node "included[0].links.downscale_675x500.href" should exist
    And the JSON node "included[0].links.placeholder_30x30.href" should exist

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
          "links":
          {
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
    Given I am on "/G70VW4Y9sP/jsonapi/node/game/a0b7c853-c891-487f-84f9-74dfbce9fa63?include=video"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "included" should have 1 element
    And the JSON node "included[0].type" should be equal to "media--remote_video"
    And the JSON node "included[0].attributes.field_media_oembed_video" should exist
    And the JSON node "included[0].attributes.field_media_oembed_video" should be equal to 'https://www.youtube.com/watch?v=y-Vgumda_mY'
