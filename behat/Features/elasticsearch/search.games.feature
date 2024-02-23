@elasticsearch
Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to retrieve JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Accessing the Games Resource should require the page parameter.
    When I request "http://api.gos.test/search/games"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "message": "Something went wrong."
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"page": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"page[0]": "This value should not be null."}
      }
      """

  Scenario: Games Resource mandatory page parameter should be zero or positive.
    When I request "http://api.gos.test/search/games?page=-1"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "message": "Something went wrong."
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"page": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"page[0]": "This value should be greater than or equal to 0."}
      }
      """

  Scenario: Games Resource should respond with a paginated subset of games.
    When I request "http://api.gos.test/search/games?page=0"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {
          "total": {
            "value": 4
          }
        }
      }
      """
    Then the response body contains JSON:
      """
      {
        "hits": {"hits": "@arrayLength(4)"}
      }
      """

  Scenario: The Games Resource facets/aggregations should follow a strict given structure.
    When I request "http://api.gos.test/search/games?page=0" using HTTP GET
    Then the response body contains JSON:
      """
      {
        "hits": {
          "total": {
            "value": "@variableType(integer)",
            "relation": "eq"
          },
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
                "changed": "@variableType(integer|string)",
                "players": {
                  "min": "@variableType(integer|null)",
                  "max": "@variableType(integer|null)"
                },
                "id": "@variableType(string)",
                "medias": [
                  {
                    "width": "@variableType(integer|string)",
                    "height": "@variableType(integer|string)",
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
