<?php

namespace Drupal\drudog\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\JsonPath;
use Flow\JSONPath\JSONPath as JSONPathSelector;

/**
 * Alters the JSON to make it easier to import.
 *
 * @DataParser(
 *   id = "drudog_jsonpath",
 *   title = @Translation("Drudog JSONPath")
 * )
 */
class DrudogJsonPath extends JsonPath {

  /**
   * Retrieves the JSON data, alters it and returns it as an array.
   *
   * @param string $url
   *   URL of a JSON feed.
   *
   * @return array
   *   The selected data to be iterated.
   *
   * @throws \GuzzleHttp\Exception\RequestException
   * @throws \Flow\JSONPath\JSONPathException
   */
  protected function getSourceData($url) {
    $response = $this->getDataFetcherPlugin()->getResponseContent($url);

    // Convert objects to associative arrays.
    $source_data = json_decode($response);

    // If json_decode() has returned NULL, it might be that the data isn't
    // valid utf8 - see http://php.net/manual/en/function.json-decode.php#86997.
    if (is_null($source_data)) {
      $utf8response = utf8_encode($response);
      $source_data = json_decode($utf8response);
    }

    // Apply our own alterations.
    $this->alterSourceData($source_data);

    $source_data = (new JSONPathSelector($source_data))->find($this->itemSelector);

    return $source_data->data();
  }

  /**
   * Extract beer id to each compound field.
   *
   * We add the beer id and delta to each ingredient and mash step to make it
   * easier to import in to paragraphs.
   *
   * @param array $source_data
   *   The associative array of source data.
   */
  private function alterSourceData(&$source_data) {
    foreach ($source_data as $key => $data) {
      if (isset($data->id)) {
        // Gather all the compound fields we need to alter.
        $fields = [
          $data->method->mash_temp,
          $data->ingredients->malt,
          $data->ingredients->hops,
        ];

        // Add the beer id and delta to the compound fields.
        foreach ($fields as $field) {
          foreach ($field as $delta => $item) {
            $item->beer_id = $data->id;
            $item->delta = $delta;
          }
        }

        // Additional check for null mash temp for #169.
        if (isset($data->method->mash_temp[0]->temp) && is_null($data->method->mash_temp[0]->temp->value)) {
          unset($data->method->mash_temp[0]);
        }
      }
    }
  }

}
