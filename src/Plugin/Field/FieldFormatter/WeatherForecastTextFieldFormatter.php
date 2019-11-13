<?php

namespace Drupal\withpulp_technical_assessment\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

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
    $elements = [];
    if (!empty($items->getValue())) {
      foreach ($items as $delta => $item) {
        // Call weather forecast service to get weather data.
        $weather_data_service = \Drupal::service('withpulp.weather_forecast');
        // Hardcoding country because this module should only support USA cities.
        $weather_data = $weather_data_service->getWeatherForecast($item->value, 'US');
        $elements[$delta] = [
          '#theme' => 'weather_data_field_formatter',
          '#weather_data' => $weather_data,
        ];
      }
    }
    return $elements;
  }

}
