@elasticsearch
Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to retrieve JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Accessing the Games Resource should require the page parameter.
    Given I send a "GET" request to "http://api.gos.test/search/games"
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "message" should be equal to "Something went wrong."
    And the JSON node "errors" should have 1 element
    And the JSON node "errors.page" should have 1 element
    And the JSON node "errors.page[0]" should be equal to 'This value should not be null.'

  Scenario: Games Resource mandatory page parameter should be zero or positive.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=-1"
    Then the response status code should be 500
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "message" should be equal to "Something went wrong with Elasticsearch."
    And the JSON node "errors" should have 1 element
    And the JSON node "errors[0]" should be equal to '{"error":{"root_cause":[{"type":"illegal_argument_exception","reason":"[from] parameter cannot be negative"}],"type":"illegal_argument_exception","reason":"[from] parameter cannot be negative"},"status":400}'

  Scenario: Games Resource should respond with a paginated subset of games.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "hits.total" should be equal to "4"
    And the JSON node "hits.hits" should have 4 elements

  Scenario: The Games Resource facets/aggregations should follow a strict given structure.
    Given I request "http://api.gos.test/search/games?page=0"
    Then the response body contains JSON:
      """
      {
        "hits": {
          "total": "@variableType(integer)",
          "max_score": "@variableType(integer)",
          "hits": [
            {
              "_score": "@variableType(integer)",
              "_source": {
                "uuid": "@variableType(string)",
                "is_published": "@variableType(boolean)",
                "title": "@variableType(string)",
                "desc": "@variableType(string)",
                "bundle": "@variableType(string)",
                "path": "@variableType(string)",
                "changed": "@variableType(string)",
                "id": "@variableType(string)",
                "medias": [
                  {
                    "width": "@variableType(string)",
                    "height": "@variableType(string)",
                    "href": "@variableType(string)",
                    "links": {
                      "3x2_660x440": "@variableType(object)",
                      "3x2_330x220": "@variableType(object)",
                      "placeholder_30x30": "@variableType(object)"
                    }
                  }
                ],
                "releases_years": [
                  {
                    "year": "@variableType(integer)"
                  }
                ],
                "releases": [
                  {
                    "date": "@variableType(string)",
                    "platform_slug": "@variableType(string)",
                    "state": "@variableType(string)"
                  }
                ],
                "studios": [
                  {
                    "path": "@variableType(string)",
                    "name": "@variableType(string)",
                    "uuid": "@variableType(string)"
                  }
                ],
                "genres": [
                  {
                    "slug": "@variableType(string)"
                  }
                ],
                "stores": [
                  {
                    "slug": "@variableType(string)",
                    "link": "@variableType(string)"
                  }
                ],
                "locations": [
                  {
                    "slug": "@variableType(string)"
                  }
                ],
                "people": "@variableType(array)"
              }
            }
          ]
        },
        "aggregations": "@variableType(object)"
      }
      """
