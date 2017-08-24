<?php

namespace Drupal\social_auth_paypal\Settings;

/**
 * Defines an interface for Social Auth Paypal settings.
 */
interface PaypalAuthSettingsInterface {

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

}
