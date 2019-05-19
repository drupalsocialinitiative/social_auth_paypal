<?php

namespace Drupal\social_auth_paypal\Plugin\Network;

use Drupal\Core\Url;
use Drupal\social_api\SocialApiException;
use Drupal\social_auth\Plugin\Network\NetworkBase;
use Drupal\social_auth_paypal\Settings\PaypalAuthSettings;
use Stevenmaguire\OAuth2\Client\Provider\Paypal;

/**
 * Defines a Network Plugin for Social Auth Paypal.
 *
 * @package Drupal\simple_paypal_connect\Plugin\Network
 *
 * @Network(
 *   id = "social_auth_paypal",
 *   social_network = "Paypal",
 *   type = "social_auth",
 *   handlers = {
 *     "settings": {
 *       "class": "\Drupal\social_auth_paypal\Settings\PaypalAuthSettings",
 *       "config_id": "social_auth_paypal.settings"
 *     }
 *   }
 * )
 */
class PaypalAuth extends NetworkBase implements PaypalAuthInterface {

  /**
   * Sets the underlying SDK library.
   *
   * @return \Stevenmaguire\OAuth2\Client\Provider\Paypal|false
   *   The initialized 3rd party library instance.
   *   False if library could not be initialized.
   *
   * @throws SocialApiException
   *   If the SDK library does not exist.
   */
  protected function initSdk() {

    $class_name = 'Stevenmaguire\OAuth2\Client\Provider\Paypal';
    if (!class_exists($class_name)) {
      throw new SocialApiException(sprintf('The Paypal Library for the league oAuth not found. Class: %s.', $class_name));
    }

    /* @var \Drupal\social_auth_paypal\Settings\PaypalAuthSettings $settings */
    $settings = $this->settings;

    if ($this->validateConfig($settings)) {
      // All these settings are mandatory.
      $league_settings = [
        'clientId' => $settings->getClientId(),
        'clientSecret' => $settings->getClientSecret(),
        'redirectUri' => Url::fromRoute('social_auth_paypal.callback')->setAbsolute()->toString(),
        'isSandbox'  => $settings->isSandbox(),
      ];

      // Proxy configuration data for outward proxy.
      $proxyUrl = $this->siteSettings->get('http_client_config')['proxy']['http'];
      if ($proxyUrl) {
        $league_settings['proxy'] = $proxyUrl;
      }

      return new Paypal($league_settings);
    }

    return FALSE;
  }

  /**
   * Checks that module is configured.
   *
   * @param \Drupal\social_auth_paypal\Settings\PaypalAuthSettings $settings
   *   The Paypal auth settings.
   *
   * @return bool
   *   True if module is configured.
   *   False otherwise.
   */
  protected function validateConfig(PaypalAuthSettings $settings) {
    $client_id = $settings->getClientId();
    $client_secret = $settings->getClientSecret();
    if (!$client_id || !$client_secret) {
      $this->loggerFactory
        ->get('social_auth_paypal')
        ->error('Define Client ID and Client Secret on module settings.');
      return FALSE;
    }

    return TRUE;
  }

}
