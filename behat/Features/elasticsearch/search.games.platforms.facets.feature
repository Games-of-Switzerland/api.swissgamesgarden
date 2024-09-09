@elasticsearch
@debug
  Feature: Retrieve Games Platforms facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Platforms in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Platforms facets/aggregations should follow a strict given structure.
    When I request "http://api.gos.test/search/games?page=0"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": "@variableType(object)",
        "aggregations": {
          "aggs_all": {
            "all_filtered_platforms": {
              "doc_count": "@variableType(integer)",
              "all_nested_platforms": {
                "doc_count": "@variableType(integer)",
                "platforms_name_keyword": {
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

  Scenario: The Platforms facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request "http://api.gos.test/search/games?page=0&platforms[]=pc"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"uuid": "08952aa6-e079-496a-8efa-cbb8465d9315"}
          },
          "hits[1]": {
            "_source": {"uuid": "a0b7c853-c891-487f-84f9-74dfbce9fa63"}
          }
        }
      }
      """

  Scenario Outline: The Platforms facets/aggregations should use the same filter as the global query - without itself as filter.
    When I request <url>
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "aggregations": {
          "aggs_all": {
            "all_filtered_platforms": {
              "all_nested_platforms": {
                "platforms_name_keyword": {
                  "buckets": "@arrayLength(8)"
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
            "all_filtered_platforms": {
              "all_nested_platforms": {
                "platforms_name_keyword": {
                  "buckets[0]": {
                    "key": "ios",
                    "doc_count": 2
                  },
                  "buckets[1]": {
                    "key": "mac",
                    "doc_count": 2
                  },
                  "buckets[2]": {
                    "key": "pc",
                    "doc_count": 2
                  },
                  "buckets[3]": {
                    "key": "android",
                    "doc_count": 1
                  },
                  "buckets[4]": {
                    "key": "nintendo_3ds",
                    "doc_count": 1
                  },
                  "buckets[5]": {
                    "key": "playstation_4",
                    "doc_count": 1
                  },
                  "buckets[6]": {
                    "key": "playstation_vita",
                    "doc_count": 1
                  },
                  "buckets[7]": {
                    "key": "xbox_one",
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
      | "http://api.gos.test/search/games?page=0&platforms[]=pc" |

    Scenario: The Platforms facets/aggregations should be affected by filtered Stores and/or Genres.
      When I request "http://api.gos.test/search/games?page=0&genres[]=puzzle"
      Then the response body contains JSON:
        """
        {
          "aggregations": {
            "aggs_all": {
              "all_filtered_platforms": {
                "all_nested_platforms": {
                  "platforms_name_keyword": {
                    "buckets": "@arrayLength(8)"
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
              "all_filtered_platforms": {
                "all_nested_platforms": {
                  "platforms_name_keyword": {
                    "buckets[0]": {
                      "key": "ios",
                      "doc_count": 1
                    },
                    "buckets[1]": {
                      "key": "android",
                      "doc_count": 0
                    },
                    "buckets[2]": {
                      "key": "mac",
                      "doc_count": 0
                    },
                    "buckets[3]": {
                      "key": "nintendo_3ds",
                      "doc_count": 0
                    },
                    "buckets[4]": {
                      "key": "pc",
                      "doc_count": 0
                    },
                    "buckets[5]": {
                      "key": "playstation_4",
                      "doc_count": 0
                    },
                    "buckets[6]": {
                      "key": "playstation_vita",
                      "doc_count": 0
                    },
                    "buckets[7]": {
                      "key": "xbox_one",
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
              "all_filtered_platforms": {
                "all_nested_platforms": {
                  "platforms_name_keyword": {
                    "buckets": "@arrayLength(8)"
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
              "all_filtered_platforms": {
                "all_nested_platforms": {
                  "platforms_name_keyword": {
                    "buckets[0]": {
                      "key": "mac",
                      "doc_count": 1
                    },
                    "buckets[1]": {
                      "key": "pc",
                      "doc_count": 1
                    },
                    "buckets[2]": {
                      "key": "playstation_4",
                      "doc_count": 1
                    },
                    "buckets[3]": {
                      "key": "xbox_one",
                      "doc_count": 1
                    },
                    "buckets[4]": {
                      "key": "android",
                      "doc_count": 0
                    },
                    "buckets[5]": {
                      "key": "ios",
                      "doc_count": 0
                    },
                    "buckets[6]": {
                      "key": "nintendo_3ds",
                      "doc_count": 0
                    },
                    "buckets[7]": {
                      "key": "playstation_vita",
                      "doc_count": 0
                    }
                  }
                }
              }
            }
          }
        }
        """
