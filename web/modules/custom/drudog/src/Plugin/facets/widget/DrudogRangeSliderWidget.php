<?php

namespace Drupal\drudog\Plugin\facets\widget;

use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\ProcessorInterface;
use Drupal\facets_range_widget\Plugin\facets\widget\RangeSliderWidget;

/**
 * Customised range slider widget.
 *
 * Appends "+" to the last label to indicate we actually include higher values
 * in the search, but are restricting the visible range.
 */
class DrudogRangeSliderWidget extends RangeSliderWidget {

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet) {
    $build = parent::build($facet);
    $processors = $facet->getProcessorsByStage(ProcessorInterface::STAGE_PRE_QUERY);
    if (isset($processors['loose_range_slider'])) {
      $facet_settings = &$build['#attached']['drupalSettings']['facets']['sliders'][$facet->id()];
      $labels =& $facet_settings['labels'];
      $labels[count($labels) - 1] .= '+';
    }
    return $build;
  }

}
