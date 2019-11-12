<?php

namespace Drupal\withpulp_technical_assessment\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use GuzzleHttp\Exception\ClientException;

/**
 * Plugin implementation of the 'giphy_result' formatter.
 *
 * @FieldFormatter(
 *   id = "weather_forcast_field",
 *   label = @Translation("Weather Forecast Field"),
 *   field_types = {
 *     "string",
 *     "computed",
 *     "computed_string",
 *   }
 * )
 */
class WeatherForecastTextFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays the weather forecast result.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $client = \Drupal::httpClient();
    // Idealy we should have a config form to update or change this url.
    // Hard coding this for test assessment.
    $weather_api_url = 'https://api.openweathermap.org/data/2.5/weather';
    foreach ($items as $delta => $item) {
      // Prepare api request parameters.
      // Hard coding the api keys for test assessment.
      // Ideally sensitive information like api keys, secret keys should be set
      // As a config object in settings.php file.
      $form_params = [
        'q' => $item->value . ', us',
        'appid' => 'b131c56e1999bbba9580c528d56a8d88',
      ];
      // Request api url and get response.
      try {
        $response = $client->request('GET', $weather_api_url, ['query' => $form_params]);
        $api_content = $response->getBody()->getContents();
        $content = json_decode($api_content, TRUE);
      }
      catch (ClientException $e) {
        $exception_content = $e->getResponse()->getBody()->getContents();
        $content = json_decode($exception_content, TRUE);
      }
      // If there is some weather data as the API response.
      if (is_array($content['weather']) && !empty($content['weather'])) {
        // Format sunrise and sunset time.
        $content['date'] = date("D d,m Y");
        $content['sunrise'] = date('H:i:s', $content['sys']['sunrise']);
        $content['sunset'] = date('H:i:s', $content['sys']['sunset']);
      }

      foreach ($items as $delta => $item) {
        $url = $item->url;
        $elements[$delta] = [
          '#theme' => 'weather_data_field_formatter',
          '#weather_data' => $content,
        ];
      }
    }

    return $elements;
  }

}
