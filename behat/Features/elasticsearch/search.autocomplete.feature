@elasticsearch
Feature: Retrieve Autocomplete Wide (People, Studio & Games) items from Elasticsearch
  In order to use an Autocomplete Search API
  As a client software developer
  I need to be able to retrieve JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Autocomplete Resource without search parameter return as most Games, People and Studio as the limit-per-bundle allowed (5).
    Given I request "http://api.gos.test/autocomplete"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {"total": 8}
      }
      """

  Scenario: Autocomplete Resource should respond with specific Elasticsearch structure.
    When I request "http://api.gos.test/autocomplete"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "bundles": {
            "bundle": {
              "buckets": "@arrayLength(3)"
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
              "buckets": [
                {
                  "key": "game",
                  "doc_count": 4,
                  "top": {
                    "hits": {"hits": "@arrayLength(4)"}
                  }
                },
                {
                  "key": "people",
                  "doc_count": 3,
                  "top": {
                    "hits": {"hits": "@arrayLength(3)"}
                  }
                },
                {
                  "key": "studio",
                  "doc_count": 1,
                  "top": {
                    "hits": {"hits": "@arrayLength(1)"}
                  }
                }
              ]
            }
          }
        }
      }
      """
