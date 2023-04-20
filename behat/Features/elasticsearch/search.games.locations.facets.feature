@elasticsearch
Feature: Retrieve Games Locations facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Locations in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Locations facets/aggregations should follow a strict given structure.
    When I request "http://api.gos.test/search/games?page=0"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": "@variableType(object)",
        "aggregations": {
          "aggs_all": {
            "all_filtered_locations": {
              "doc_count": "@variableType(integer)",
              "all_nested_locations": {
                "doc_count": "@variableType(integer)",
                "locations_name_keyword": {
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

  Scenario Outline: The Locations facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request <url>
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_locations": {
              "all_nested_locations": {
                "locations_name_keyword": {
                  "buckets": "@arrayLength(4)"
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
            "all_filtered_locations": {
              "all_nested_locations": {
                "locations_name_keyword": {
                  "buckets[0]": {
                    "key": "fribourg",
                    "doc_count": 1
                  },
                  "buckets[1]": {
                    "key": "lausanne",
                    "doc_count": 1
                  },
                  "buckets[2]": {
                    "key": "zurich",
                    "doc_count": 1
                  },
                  "buckets[3]": {
                    "key": "geneva",
                    "doc_count": 0
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
      | "http://api.gos.test/search/games?page=0&locations[]=zurich" |

    Scenario: The Locations facets/aggregations should be affected by filtered Stores and/or Platforms.
      When I request "http://api.gos.test/search/games?page=0&platforms[]=ios"
      Then the response body contains JSON:
        """
        {
          "aggregations": {
            "aggs_all": {
              "all_filtered_locations": {
                "all_nested_locations": {
                  "locations_name_keyword": {
                    "buckets": "@arrayLength(4)"
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
              "all_filtered_locations": {
                "all_nested_locations": {
                  "locations_name_keyword": {
                    "buckets[0]": {
                      "key": "lausanne",
                      "doc_count": 1
                    },
                    "buckets[1]": {
                      "key": "fribourg",
                      "doc_count": 0
                    },
                    "buckets[2]": {
                      "key": "geneva",
                      "doc_count": 0
                    },
                    "buckets[3]": {
                      "key": "zurich",
                      "doc_count": 0
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
            "all_filtered_locations": {
              "all_nested_locations": {
                "locations_name_keyword": {
                  "buckets": "@arrayLength(4)"
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
            "all_filtered_locations": {
              "all_nested_locations": {
                "locations_name_keyword": {
                  "buckets[0]": {
                    "key": "zurich",
                    "doc_count": 1
                  },
                  "buckets[1]": {
                    "key": "fribourg",
                    "doc_count": 0
                  },
                  "buckets[2]": {
                    "key": "geneva",
                    "doc_count": 0
                  },
                  "buckets[3]": {
                    "key": "lausanne",
                    "doc_count": 0
                  }
                }
              }
            }
          }
        }
      }
      """
