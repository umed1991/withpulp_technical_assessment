<?php

namespace Drupal\withpulp_technical_assessment;

use GuzzleHttp\Exception\ClientException;

/**
 * CowService is a simple exampe of a Drupal 8 service.
 */
class WeatherForecastService {

  /**
   * Real photo of our cow.
   */
  public function getWeatherForecast($city = NULL, $country = NULL) {
    $weather_forcast_data = [];
    if (isset($city) && isset($country)) {
      $client = \Drupal::httpClient();
      // Idealy we should have a config form to update or change this url.
      // Hard coding this for test assessment.
      $weather_api_url = 'https://api.openweathermap.org/data/2.5/weather';
      // Prepare api request parameters.
      // Hard coding the api keys for test assessment.
      // Ideally sensitive information like api keys, secret keys should be set
      // As a config object in settings.php file.
      $form_params = [
        'q' => $city . ', ' . $country,
        'appid' => 'b131c56e1999bbba9580c528d56a8d88',
      ];
      // Request api url and get response.
      try {
        $response = $client->request('GET', $weather_api_url, ['query' => $form_params]);
        $api_content = $response->getBody()->getContents();
        $weather_forcast_data = json_decode($api_content, TRUE);
      }
      catch (ClientException $e) {
        $exception_content = $e->getResponse()->getBody()->getContents();
        $weather_forcast_data = json_decode($exception_content, TRUE);
      }

      // If there is some weather data as the API response.
      if (is_array($weather_forcast_data['weather']) && !empty($weather_forcast_data['weather'])) {
        // Format sunrise and sunset time.
        $weather_forcast_data['date'] = date("D d,m Y");
        $weather_forcast_data['sunrise'] = date('H:i:s', $weather_forcast_data['sys']['sunrise']);
        $weather_forcast_data['sunset'] = date('H:i:s', $weather_forcast_data['sys']['sunset']);
      }
    }
    return $weather_forcast_data;
  }

}
