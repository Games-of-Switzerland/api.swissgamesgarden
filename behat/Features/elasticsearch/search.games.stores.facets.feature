@elasticsearch
  Feature: Retrieve Games Platforms facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Stores in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Stores facets/aggregations should follow a strict given structure.
    When I request "http://api.gos.test/search/games?page=0"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": "@variableType(object)",
        "aggregations": {
          "aggs_all": {
            "all_filtered_stores": {
              "doc_count": "@variableType(integer)",
              "all_nested_stores": {
                "doc_count": "@variableType(integer)",
                "stores_name_keyword": {
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

  Scenario: The Stores facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request "http://api.gos.test/search/games?page=0&stores[]=apple_store"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"uuid": "f990d6af-d50d-4b35-a79a-72a1e12a7422"}
          }
        }
      }
      """

  Scenario Outline: The Stores facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request <url>
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_stores": {
              "all_nested_stores": {
                "stores_name_keyword": {
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
            "all_filtered_stores": {
              "all_nested_stores": {
                "stores_name_keyword": {
                  "buckets[0]": {
                    "key": "apple_store",
                    "doc_count": 1
                  },
                  "buckets[1]": {
                    "key": "custom",
                    "doc_count": 1
                  },
                  "buckets[2]": {
                    "key": "google_play_store",
                    "doc_count": 1
                  },
                  "buckets[3]": {
                    "key": "steam",
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
      | "http://api.gos.test/search/games?page=0&stores[]=apple_store" |

    Scenario: The Stores facets/aggregations should be affected by filtered Platforms and/or Genres.
      When I request "http://api.gos.test/search/games?page=0&platforms[]=pc"
      Then the response body contains JSON:
        """
        {
          "aggregations": {
            "aggs_all": {
              "all_filtered_stores": {
                "all_nested_stores": {
                  "stores_name_keyword": {
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
              "all_filtered_stores": {
                "all_nested_stores": {
                  "stores_name_keyword": {
                    "buckets[0]": {
                      "key": "custom",
                      "doc_count": 1
                    },
                    "buckets[1]": {
                      "key": "steam",
                      "doc_count": 1
                    },
                    "buckets[2]": {
                      "key": "apple_store",
                      "doc_count": 0
                    },
                    "buckets[3]": {
                      "key": "google_play_store",
                      "doc_count": 0
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
              "all_filtered_stores": {
                "all_nested_stores": {
                  "stores_name_keyword": {
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
              "all_filtered_stores": {
                "all_nested_stores": {
                  "stores_name_keyword": {
                    "buckets[0]": {
                      "key": "apple_store",
                      "doc_count": 1
                    },
                    "buckets[1]": {
                      "key": "google_play_store",
                      "doc_count": 1
                    },
                    "buckets[2]": {
                      "key": "custom",
                      "doc_count": 0
                    },
                    "buckets[3]": {
                      "key": "steam",
                      "doc_count": 0
                    }
                  }
                }
              }
            }
          }
        }
        """
