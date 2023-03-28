@elasticsearch
  Feature: Retrieve Games Cantons facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Cantons in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Cantons facets/aggregations should follow a strict given structure.
    When I request "http://api.gos.test/search/games?page=0"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": "@variableType(object)",
        "aggregations": {
          "aggs_all": {
            "all_filtered_cantons": {
              "doc_count": "@variableType(integer)",
              "all_nested_cantons": {
                "doc_count": "@variableType(integer)",
                "cantons_name_keyword": {
                  "buckets": [{
                    "key": "@variableType(string)",
                    "doc_count": "@variableType(integer)"
                  }]
                }
              }
            }
          }
        }
      }
      """

  Scenario Outline: The Cantons facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request <url>
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_cantons": {
              "all_nested_cantons": {
                "cantons_name_keyword": {
                  "buckets": "@arrayLength(2)"
                }
              }
            }
          }
        }
      }
      """
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_cantons": {
              "all_nested_cantons": {
                "cantons_name_keyword": {
                  "buckets[0]": {
                    "key": "geneva",
                    "doc_count": 2
                  },
                  "buckets[1]": {
                    "key": "vaud",
                    "doc_count": 1
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
      | "http://api.gos.test/search/games?page=0" |
      | "http://api.gos.test/search/games?page=0&cantons[]=geneva" |

  Scenario: The Cantons facets/aggregations should be affected by filtered Stores and/or Platforms.
    When I request "http://api.gos.test/search/games?page=0&platforms[]=ios"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_cantons": {
              "all_nested_cantons": {
                "cantons_name_keyword": {
                  "buckets": "@arrayLength(2)"
                }
              }
            }
          }
        }
      }
      """
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_cantons": {
              "all_nested_cantons": {
                "cantons_name_keyword": {
                  "buckets[0]": {
                    "key": "geneva",
                    "doc_count": 1
                  },
                  "buckets[1]": {
                    "key": "vaud",
                    "doc_count": 1
                  }
                }
              }
            }
          }
        }
      }
      """
    When I request "http://api.gos.test/search/games?page=0&stores[]=steam"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_cantons": {
              "all_nested_cantons": {
                "cantons_name_keyword": {
                  "buckets": "@arrayLength(2)"
                }
              }
            }
          }
        }
      }
      """
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_cantons": {
              "all_nested_cantons": {
                "cantons_name_keyword": {
                  "buckets[0]": {
                    "key": "geneva",
                    "doc_count": 0
                  },
                  "buckets[1]": {
                    "key": "vaud",
                    "doc_count": 0
                  }
                }
              }
            }
          }
        }
      }
      """
