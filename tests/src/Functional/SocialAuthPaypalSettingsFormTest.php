<?php

namespace Drupal\Tests\social_auth_paypal\Functional;

use Drupal\social_api\SocialApiSettingsFormBaseTest;

/**
 * Test Social Auth Paypal settings form.
 *
 * @group social_auth
 *
 * @ingroup social_auth_paypal
 */
class SocialAuthPaypalSettingsFormTest extends SocialApiSettingsFormBaseTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['social_auth_paypal'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->module = 'social_auth_paypal';
    $this->socialNetwork = 'paypal';
    $this->moduleType = 'social-auth';

    parent::setUp();
  }

  /**
   * {@inheritdoc}
   */
  public function testIsAvailableInIntegrationList() {
    $this->fields = ['app_key', 'app_secret'];

    parent::testIsAvailableInIntegrationList();
  }

  /**
   * {@inheritdoc}
   */
  public function testSettingsFormSubmission() {
    $this->edit = [
      'app_key' => $this->randomString(10),
      'app_secret' => $this->randomString(10),
    ];

    parent::testSettingsFormSubmission();
  }

}
