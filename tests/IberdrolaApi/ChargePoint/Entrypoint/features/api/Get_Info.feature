Feature: Get Charge Point Info

  Scenario: Get Charge Point Info
    When I send a GET request to "/charge-point/123456"
    Then the response status code should be 200