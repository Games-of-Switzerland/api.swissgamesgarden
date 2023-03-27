@elasticsearch
  Feature: Retrieve Games Release Years facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Release Years in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Release Years facets/aggregations should follow a strict given structure.
    When I request "http://api.gos.test/search/games?page=0"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": "@variableType(object)",
        "aggregations": {
          "aggs_all": {
            "all_filtered_release_years_histogram": {
              "doc_count": "@variableType(integer)",
              "all_nested_release_years": {
                "doc_count": "@variableType(integer)",
                "releases_over_time": {
                  "buckets": [{
                    "key_as_string": "@variableType(string)",
                    "doc_count": "@variableType(integer)"
                  }]
                }
              }
            }
          }
        }
      }
      """

  Scenario Outline: The Release Years facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request <url>
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
        """
        {
          "aggregations": {
            "aggs_all": {
              "all_filtered_release_years_histogram": {
                "all_nested_release_years": {
                  "releases_over_time": {
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
            "all_filtered_release_years_histogram": {
              "all_nested_release_years": {
                "releases_over_time": {
                  "buckets[0]": {
                    "key_as_string": "2017",
                    "doc_count": 1
                  },
                  "buckets[1]": {
                    "key_as_string": "2018",
                    "doc_count": 2
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
      | "http://api.gos.test/search/games?page=0&release_year=2017" |

    Scenario: The Release Years facets/aggregations should be affected by filtered Stores and/or Platforms.
    When I request "http://api.gos.test/search/games?page=0&platforms[]=ios"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_release_years_histogram": {
              "all_nested_release_years": {
                "releases_over_time": {
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
              "all_filtered_release_years_histogram": {
                "all_nested_release_years": {
                  "releases_over_time": {
                    "buckets[0]": {
                      "key_as_string": "2017",
                      "doc_count": 1
                    },
                    "buckets[1]": {
                      "key_as_string": "2018",
                      "doc_count": 1
                    }
                  }
                }
              }
            }
          }
        }
        """

    When I request "http://api.gos.test/search/games?page=0&platforms[]=pc"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_release_years_histogram": {
              "all_nested_release_years": {
                "releases_over_time": {
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
            "all_filtered_release_years_histogram": {
              "all_nested_release_years": {
                "releases_over_time": {
                  "buckets[0]": {
                    "key_as_string": "2017",
                    "doc_count": 1
                  },
                  "buckets[1]": {
                    "key_as_string": "2018",
                    "doc_count": 2
                  }
                }
              }
            }
          }
        }
      }
      """
