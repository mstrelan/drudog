<?php

namespace Drupal\Tests\drudog\ExistingSiteJavascript;

use weitzman\DrupalTestTraits\ExistingSiteWebDriverTestBase;

/**
 * Sample class to test the existing site.
 *
 * @group drudog
 */
class ExistingSiteTest extends ExistingSiteWebDriverTestBase {

  /**
   * Checks that a node exists.
   */
  public function testNodeExists() {
    $this->visit('/node/1');
    $web_assert = $this->assertSession();
    $web_assert->pageTextContains('Buzz');
  }

}
