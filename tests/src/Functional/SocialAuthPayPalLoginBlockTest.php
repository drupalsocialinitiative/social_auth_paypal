<?php

namespace Drupal\Tests\social_auth_paypal\Functional;

use Drupal\Tests\social_auth\Functional\SocialAuthTestBase;

/**
 * Test that path to authentication route exists in Social Auth Login block.
 *
 * @group social_auth
 *
 * @ingroup social_auth_paypal
 */
class SocialAuthPayPalLoginBlockTest extends SocialAuthTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block', 'social_auth_paypal'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->provider = 'paypal';
  }

  /**
   * Test that the path is included in the login block.
   *
   * @throws \Behat\Mink\Exception\ResponseTextException
   */
  public function testLinkExistsInBlock() {
    $this->checkLinkToProviderExists();
  }

}
