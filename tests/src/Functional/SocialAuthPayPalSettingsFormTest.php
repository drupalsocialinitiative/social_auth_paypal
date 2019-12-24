<?php

namespace Drupal\Tests\social_auth_paypal\Functional;

use Drupal\Tests\social_auth\Functional\SocialAuthTestBase;

/**
 * Test Social Auth PayPal settings form.
 *
 * @group social_auth
 *
 * @ingroup social_auth_paypal
 */
class SocialAuthPayPalSettingsFormTest extends SocialAuthTestBase {

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
    $this->provider = 'paypal';

    parent::setUp();
  }

  /**
   * Test if implementer is shown in the integration list.
   */
  public function testIsAvailableInIntegrationList() {
    $this->fields = ['client_id', 'client_secret', 'is_sandbox'];

    $this->checkIsAvailableInIntegrationList();
  }

  /**
   * Test if permissions are set correctly for settings page.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testPermissionForSettingsPage() {
    $this->checkPermissionForSettingsPage();
  }

  /**
   * Test settings form submission.
   */
  public function testSettingsFormSubmission() {
    $value = '';
    if (rand(0, 1)) {
      $value = 'true';
    }
    else {
      $value = 'false';
    }
    $this->edit = [
      'client_id' => $this->randomString(10),
      'client_secret' => $this->randomString(10),
      'is_sandbox' => $value,
    ];

    $this->checkSettingsFormSubmission();
  }

}
