<?php

namespace Drupal\Tests\withpulp_technical_assessment\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test the Giphy Field formatter.
 *
 * @group osworkshop_technical_assessment
 */
class WeatherForecastTextFieldFormatterTest extends BrowserTestBase {

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  public static $modules = [
    'field',
    'withpulp_technical_assessment',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Tests the setting form.
   */
  public function testWeatherFieldFormatter() {
    // Create the user with the admin permission.
    $admin_user = $this->drupalCreateUser([
      'administer content',
    ]);

    // Start the session.
    $session = $this->assertSession();

    // Login as our account.
    $this->drupalLogin($admin_user);

    // Create a node of technical assessment demo content type.
    $edit = [];
    $edit['title[0][value]'] = $this->randomMachineName(8);
    $edit['field_giphy_result[0][value]'] = 'New York';
    $this->drupalPostForm('node/add/article', $edit, t('Save'));

    // Verify that the creation message contains a link to a node.
    $view_link = $this->xpath('//div[@class="messages"]//a[contains(@href, :href)]', [':href' => 'node/']);
    $this->assert(isset($view_link), 'The message area contains a link to a node');

    // Verify that the node was created.
    $node = $this->drupalGetNodeByTitle($edit['title[0][value]']);
    $this->assertTrue($node, 'Node found in database.');
    // Go to node view page.
    $this->drupalGet('node/' . $node->id());

    // After this not sure how can I verify gif on the node view page.
    $temprature_link = $this->xpath('//div[@class="temp"]');
    $this->assert(isset($temprature_link), 'Weather widget found with temperature details.');

  }

}
