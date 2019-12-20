<?php

namespace Drupal\social_auth_paypal;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\social_auth\AuthManager\OAuth2Manager;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * Contains all the logic for PayPal OAuth2 authentication.
 */
class PayPalAuthManager extends OAuth2Manager {

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Used for accessing configuration object factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   */
  public function __construct(ConfigFactory $configFactory, LoggerChannelFactoryInterface $logger_factory) {
    parent::__construct($configFactory->get('social_auth_paypal.settings'), $logger_factory);
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate() {
    try {
      $this->setAccessToken($this->client->getAccessToken('authorization_code',
        ['code' => $_GET['code']]));
    }
    catch (IdentityProviderException $e) {
      $this->loggerFactory->get('social_auth_paypal')
        ->error('There was an error during authentication. Exception: ' . $e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getUserInfo() {
    if (!$this->user) {
      $this->user = $this->client->getResourceOwner($this->getAccessToken());
    }

    return $this->user;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthorizationUrl() {
    $scopes = [
      'profile',
      'openid',
      'email',
    ];

    $extra_scopes = $this->getScopes();
    if ($extra_scopes) {
      $scopes = array_merge($scopes, explode(',', $extra_scopes));
    }

    // Returns the URL where user will be redirected.
    return $this->client->getAuthorizationUrl([
      'scope' => $scopes,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function requestEndPoint($method, $path, $domain = NULL, array $options = []) {
    // TODO: Implement this method.
  }

  /**
   * {@inheritdoc}
   */
  public function getState() {
    return $this->client->getState();
  }

}
