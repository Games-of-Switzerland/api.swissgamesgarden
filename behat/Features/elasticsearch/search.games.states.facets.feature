@elasticsearch
  Feature: Retrieve Games Platforms facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted States in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The States facets/aggregations should follow a strict given structure.
    Given I request "http://api.gos.test/search/games?page=0"
    Then the response body contains JSON:
      """
      {
        "hits": "@variableType(object)",
        "aggregations": {
          "aggs_all": {
            "all_filtered_states": {
              "doc_count": "@variableType(integer)",
              "all_nested_states": {
                "doc_count": "@variableType(integer)",
                "states_name_keyword": {
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

  Scenario: The States facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request "http://api.gos.test/search/games?page=0&states[]=canceled"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"uuid": "a0b7c853-c891-487f-84f9-74dfbce9fa63"}
          }
        }
      }
      """

  Scenario Outline: The States facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request <url>
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_states": {
              "all_nested_states": {
                "states_name_keyword": {
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
            "all_filtered_states": {
              "all_nested_states": {
                "states_name_keyword": {
                  "buckets[0]": {
                    "key": "released",
                    "doc_count": 3
                  },
                  "buckets[1]": {
                    "key": "development",
                    "doc_count": 2
                  },
                  "buckets[2]": {
                    "key": "canceled",
                    "doc_count": 1
                  },
                  "buckets[3]": {
                    "key": "pre_release",
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
      | "http://api.gos.test/search/games?page=0&states[]=canceled" |

    Scenario: The States facets/aggregations should be affected by filtered Platforms and/or Genres.
      When I request "http://api.gos.test/search/games?page=0&platforms[]=pc"
      Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_states": {
              "all_nested_states": {
                "states_name_keyword": {
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
              "all_filtered_states": {
                "all_nested_states": {
                  "states_name_keyword": {
                    "buckets[0]": {
                      "key": "development",
                      "doc_count": 2
                    },
                    "buckets[1]": {
                      "key": "released",
                      "doc_count": 2
                    },
                    "buckets[2]": {
                      "key": "canceled",
                      "doc_count": 1
                    },
                    "buckets[3]": {
                      "key": "pre_release",
                      "doc_count": 1
                    }
                  }
                }
              }
            }
          }
        }
        """
      When I request "http://api.gos.test/search/games?page=0&genres[]=puzzle"
      Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_states": {
              "all_nested_states": {
                "states_name_keyword": {
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
              "all_filtered_states": {
                "all_nested_states": {
                  "states_name_keyword": {
                    "buckets[0]": {
                      "key": "released",
                      "doc_count": 1
                    },
                    "buckets[1]": {
                      "key": "canceled",
                      "doc_count": 0
                    },
                    "buckets[2]": {
                      "key": "development",
                      "doc_count": 0
                    },
                    "buckets[3]": {
                      "key": "pre_release",
                      "doc_count": 0
                    }
                  }
                }
              }
            }
          }
        }
        """
