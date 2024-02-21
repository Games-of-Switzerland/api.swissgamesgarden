@elasticsearch
Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to fetch sortable Games in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should be sortable by score properties.
    When I request "http://api.gos.test/search/games?page=0&sort[desc]=_score"
    Then the response code is 200
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

  Scenario: Games Resource should be sortable by title properties.
    When I request "http://api.gos.test/search/games?page=0&sort[desc]=title.keyword"
    Then the response code is 200
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
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"title": "Persephone"}
          },
          "hits[1]": {
            "_source": {"title": "Farming Simulator 19"}
          },
          "hits[2]": {
            "_source": {"title": "Farming Simulator 18"}
          },
          "hits[3]": {
            "_source": {"title": "Don't kill Her"}
          }
        }
      }
      """
    When I request "http://api.gos.test/search/games?page=0&sort[asc]=title.keyword"
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"title": "Don't kill Her"}
          },
          "hits[1]": {
            "_source": {"title": "Farming Simulator 18"}
          },
          "hits[2]": {
            "_source": {"title": "Farming Simulator 19"}
          },
          "hits[3]": {
            "_source": {"title": "Persephone"}
          }
        }
      }
      """

  Scenario: Games Resource should be sortable by release date properties.
    When I request "http://api.gos.test/search/games?page=0&sort[desc]=releases.date"
    Then the response code is 200
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
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"title": "Farming Simulator 19"}
          },
          "hits[1]": {
            "_source": {"title": "Farming Simulator 18"}
          },
          "hits[2]": {
            "_source": {"title": "Don't kill Her"}
          },
          "hits[3]": {
            "_source": {"title": "Persephone"}
          }
        }
      }
      """
    When I request "http://api.gos.test/search/games?page=0&sort[asc]=releases.date"
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"title": "Farming Simulator 18"}
          },
          "hits[1]": {
            "_source": {"title": "Farming Simulator 19"}
          },
          "hits[2]": {
            "_source": {"title": "Don't kill Her"}
          },
          "hits[3]": {
            "_source": {"title": "Persephone"}
          }
        }
      }
      """

  Scenario: Games Resource should respond with an error if the direction is incorrect.
    When I request "http://api.gos.test/search/games?page=0&sort[bar]=_score"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "errors": {"sort": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"sort[0]": "Provided direction \"bar\" is not supported. Please use \"asc\" or \"desc\"."}
      }
      """

  Scenario: Games Resource should respond with an error if the field is not sortable.
    When I request "http://api.gos.test/search/games?page=0&sort[desc]=foo"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "errors": {"sort": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"sort[0]": "Provided property \"foo\" is not sortable."}
      }
      """
