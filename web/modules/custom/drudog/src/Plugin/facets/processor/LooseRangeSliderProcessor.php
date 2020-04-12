<?php

namespace Drupal\drudog\Plugin\facets\processor;

use Drupal\facets_range_widget\Plugin\facets\processor\RangeSliderProcessor;
use Drupal\facets\FacetInterface;

/**
 * Extends RangeSliderProcessor to remove the upper bound.
 *
 * @FacetsProcessor(
 *   id = "loose_range_slider",
 *   label = @Translation("Loose range slider"),
 *   description = @Translation("Removes the upper bound of range sliders."),
 *   stages = {
 *     "pre_query" = 70
 *   }
 * )
 */
class LooseRangeSliderProcessor extends RangeSliderProcessor {

  /**
   * {@inheritdoc}
   */
  public function preQuery(FacetInterface $facet) {
    $active_items = $facet->getActiveItems();
    array_walk($active_items, function (&$item, $key, FacetInterface $facet) {
      if (is_array($item) && isset($item[1])) {
        $max_value = $facet->getWidget()['config']['max_value'];
        if ($item[1] >= $max_value) {
          $item[1] = PHP_INT_MAX;
        }
      }
    }, $facet);
    $facet->setActiveItems($active_items);
  }

}
