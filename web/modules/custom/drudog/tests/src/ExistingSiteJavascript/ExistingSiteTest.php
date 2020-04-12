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
   * Checks that the first node exists.
   */
  public function testNode1() {
    $this->visit('/node/1');
    $web_assert = $this->assertSession();
    $web_assert->pageTextContains('Buzz');
  }

  /**
   * Checks that a problematic node exists.
   *
   * The beer "AB:19" was a bit problematic for the migration. Test that the
   * page exists and doesn't show any ingredients.
   */
  public function testNode169() {
    $this->visit('/node/169');
    $web_assert = $this->assertSession();
    $web_assert->pageTextContains('AB:19');
    $web_assert->pageTextNotContains('Malt');
    $web_assert->pageTextNotContains('Hops');
    $web_assert->pageTextNotContains('Yeast');
  }

  /**
   * Checks that beers with multiple yeasts import correctly.
   */
  public function testMultipleYeasts() {
    $this->visit('/node/97');
    $web_assert = $this->assertSession();
    $web_assert->pageTextContains('Wyeast 1056');
    $web_assert->pageTextContains('Wyeast 1272');
  }

  /**
   * Tests a search for "movember".
   *
   * The search should only return the Movember result and not another random
   * beer such as "AB:12".
   */
  public function testSimpleFullTextSearch() {
    $this->visit('/beer');
    $this->submitForm([
      'search' => 'movember',
    ], 'Go');
    $web_assert = $this->assertSession();
    $web_assert->pageTextContains('Movember');
    $web_assert->pageTextNotContains('AB:12');
  }

  /**
   * Tests a search for "russian doll".
   *
   * The search should return four different Russian Doll beers and again, not
   * another random beer such as "AB:12".
   */
  public function testFullTextSearchWithMultipleResults() {
    $this->visit('/beer');
    $this->submitForm([
      'search' => 'russian doll',
    ], 'Go');
    $web_assert = $this->assertSession();
    $web_assert->pageTextContains('Barley Wine - Russian Doll');
    $web_assert->pageTextContains('Russian Doll – India Pale Ale');
    $web_assert->pageTextContains('Pale - Russian Doll');
    $web_assert->pageTextContains('Double IPA - Russian Doll');
    $web_assert->pageTextNotContains('AB:12');
  }

  /**
   * Tests a search for "paivi" transliterated from Päivi.
   */
  public function testFullTextSearchTransliteration() {
    $this->visit('/beer');
    $this->submitForm([
      'search' => 'paivi',
    ], 'Go');
    $web_assert = $this->assertSession();
    $web_assert->pageTextContains('Hello My Name Is Päivi');
  }
}
