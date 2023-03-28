@elasticsearch
Feature: Retrieve Autocomplete Wide (People, Studio & Games) items from Elasticsearch
  In order to use an Autocomplete Search API
  As a client software developer
  I need to be able to filter Documents by FullText conditions and retrieve a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Autocomplete Resource should respond with filtered games/studios/people on matching queries.
    When I request "http://api.gos.test/autocomplete?q=giants"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {"total": 3}
      }
      """
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "bundles": {
            "bundle": {
              "buckets": "@arrayLength(2)"
            }
          }
        }
      }
      """
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "bundles": {
            "bundle": {
              "buckets[0]": {
                "key": "game",
                "doc_count": 2
              },
              "buckets[1]": {
                "key": "studio",
                "doc_count": 1
              }
            }
          }
        }
      }
      """

  Scenario Outline: Autocomplete Resource should be case-insensitive and ASCII-insensitive.
    When I request <url>
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {"total": 2}
      }
      """
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "bundles": {
            "bundle": {
              "buckets": "@arrayLength(2)"
            }
          }
        }
      }
      """
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "bundles": {
            "bundle": {
              "buckets[0]": {
                "key": "game",
                "doc_count": 1,
                "top": {
                  "hits": {
                    "hits[0]": {
                      "_source": {
                        "title": "Don't kill Her"
                      }
                    }
                  }
                }
              },
              "buckets[1]": {
                "key": "people",
                "doc_count": 1,
                "top": {
                  "hits": {
                    "hits[0]": {
                      "_source": {
                        "fullname": "J\u00e9r\u00e9my \"Wuthrer\" Cuany"
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
      """
    Examples:
      | url |
      | "http://api.gos.test/autocomplete?q=WuthRER" |
      | "http://api.gos.test/autocomplete?q=jérémy" |
