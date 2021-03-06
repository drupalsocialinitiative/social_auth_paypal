<?php

namespace Drupal\social_auth_paypal\Settings;

use Drupal\social_api\Settings\SettingsBase;

/**
 * Defines methods to get Social Auth PayPal settings.
 */
class PayPalAuthSettings extends SettingsBase implements PayPalAuthSettingsInterface {

  /**
   * Client ID.
   *
   * @var string
   */
  protected $clientId;

  /**
   * Client secret.
   *
   * @var string
   */
  protected $clientSecret;

  /**
   * Whether the app is a sandbox project.
   *
   * @var string
   */
  protected $isSandbox;

  /**
   * {@inheritdoc}
   */
  public function getClientId() {
    if (!$this->clientId) {
      $this->clientId = $this->config->get('client_id');
    }

    return $this->clientId;
  }

  /**
   * {@inheritdoc}
   */
  public function getClientSecret() {
    if (!$this->clientSecret) {
      $this->clientSecret = $this->config->get('client_secret');
    }

    return $this->clientSecret;
  }

  /**
   * {@inheritdoc}
   */
  public function isSandbox() {
    return $this->config->get('is_sandbox');
  }

}
