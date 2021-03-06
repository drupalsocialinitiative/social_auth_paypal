<?php

namespace Drupal\social_auth_paypal\Settings;

/**
 * Defines an interface for Social Auth PayPal settings.
 */
interface PayPalAuthSettingsInterface {

  /**
   * Gets the client ID.
   *
   * @return string
   *   The client ID.
   */
  public function getClientId();

  /**
   * Gets the client secret.
   *
   * @return string
   *   The client secret.
   */
  public function getClientSecret();

  /**
   * Checks if the app is a sandbox.
   *
   * @return bool
   *   Whether the app is a sandbox.
   */
  public function isSandbox();

}
