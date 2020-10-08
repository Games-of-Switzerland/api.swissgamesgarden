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
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&states[]=canceled"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |

  Scenario Outline: The States facets/aggregations should use the same filter as the global query - without itself as filter.
    Given I send a "GET" request to <url>
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets" should have 4 elements
    And the JSON nodes should be equal to:
      | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[0].key | released |
      | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[0].doc_count | 6 |
      | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[1].key | development  |
      | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[1].doc_count | 2 |
      | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[2].key | canceled |
      | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[2].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[3].key | pre_release |
      | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[3].doc_count | 1 |
    Examples:
      | url |
      | "http://api.gos.test/search/games?page=0" |
      | "http://api.gos.test/search/games?page=0&states[]=canceled" |

    Scenario: The States facets/aggregations should be affected by filtered Platforms and/or Genres.
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&platforms[]=pc"
      And the JSON node "aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets" should have 4 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[0].key | released |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[0].doc_count | 5 |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[1].key | development  |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[1].doc_count | 2 |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[2].key | canceled |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[2].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[3].key | pre_release |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[3].doc_count | 1 |
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&genres[]=puzzle"
      And the JSON node "aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets" should have 4 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[0].key | released |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[0].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[1].key | canceled  |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[1].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[2].key | development |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[2].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[3].key | pre_release |
        | aggregations.aggs_all.all_filtered_states.all_nested_states.states_name_keyword.buckets[3].doc_count | 0 |
