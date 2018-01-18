<?php

namespace Drupal\social_auth_paypal\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\social_api\Plugin\NetworkManager;
use Drupal\social_auth\SocialAuthDataHandler;
use Drupal\social_auth\SocialAuthUserManager;
use Drupal\social_auth_paypal\PaypalAuthManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Returns responses for Simple Paypal Connect module routes.
 */
class PaypalAuthController extends ControllerBase {

  /**
   * The network plugin manager.
   *
   * @var \Drupal\social_api\Plugin\NetworkManager
   */
  private $networkManager;

  /**
   * The user manager.
   *
   * @var \Drupal\social_auth\SocialAuthUserManager
   */
  private $userManager;

  /**
   * The paypal authentication manager.
   *
   * @var \Drupal\social_auth_paypal\PaypalAuthManager
   */
  private $paypalManager;

  /**
   * Used to access GET parameters.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  private $request;

  /**
   * The Social Auth Data Handler.
   *
   * @var \Drupal\social_auth\SocialAuthDataHandler
   */
  private $dataHandler;

  /**
   * PaypalAuthController constructor.
   *
   * @param \Drupal\social_api\Plugin\NetworkManager $network_manager
   *   Used to get an instance of social_auth_paypal network plugin.
   * @param \Drupal\social_auth\SocialAuthUserManager $user_manager
   *   Manages user login/registration.
   * @param \Drupal\social_auth_paypal\PaypalAuthManager $paypal_manager
   *   Used to manage authentication methods.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   Used to access GET parameters.
   * @param \Drupal\social_auth\SocialAuthDataHandler $data_handler
   *   SocialAuthDataHandler object.
   */
  public function __construct(NetworkManager $network_manager,
                              SocialAuthUserManager $user_manager,
                              PaypalAuthManager $paypal_manager,
                              RequestStack $request,
                              SocialAuthDataHandler $data_handler) {

    $this->networkManager = $network_manager;
    $this->userManager = $user_manager;
    $this->paypalManager = $paypal_manager;
    $this->request = $request;
    $this->dataHandler = $data_handler;

    // Sets the plugin id.
    $this->userManager->setPluginId('social_auth_paypal');

    // Sets the session keys to nullify if user could not logged in.
    $this->userManager->setSessionKeysToNullify(['access_token', 'oauth2state']);
    $this->setting = $this->config('social_auth_paypal.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.network.manager'),
      $container->get('social_auth.user_manager'),
      $container->get('social_auth_paypal.manager'),
      $container->get('request_stack'),
      $container->get('social_auth.data_handler')
    );
  }

  /**
   * Response for path 'user/login/paypal'.
   *
   * Redirects the user to Paypal for authentication.
   */
  public function redirectToPaypal() {
    /* @var \Stevenmaguire\OAuth2\Client\Provider\Paypal|false $paypal */
    $paypal = $this->networkManager->createInstance('social_auth_paypal')->getSdk();

    // If paypal client could not be obtained.
    if (!$paypal) {
      drupal_set_message($this->t('Social Auth Paypal not configured properly. Contact site administrator.'), 'error');
      return $this->redirect('user.login');
    }

    // Paypal service was returned, inject it to $paypalManager.
    $this->paypalManager->setClient($paypal);

    // Generates the URL where the user will be redirected for Paypal login.
    $paypal_login_url = $this->paypalManager->getPaypalLoginUrl();

    $state = $this->paypalManager->getState();

    $this->dataHandler->set('oauth2state', $state);

    return new TrustedRedirectResponse($paypal_login_url);
  }

  /**
   * Response for path 'user/login/paypal/callback'.
   *
   * Paypal returns the user here after user has authenticated in Paypal.
   */
  public function callback() {
    // Checks if user cancel login via Paypal.
    $error = $this->request->getCurrentRequest()->get('error');
    if ($error == 'access_denied') {
      drupal_set_message($this->t('You could not be authenticated.'), 'error');
      return $this->redirect('user.login');
    }

    /* @var \Stevenmaguire\OAuth2\Client\Provider\Paypal|false $paypal */
    $paypal = $this->networkManager->createInstance('social_auth_paypal')->getSdk();

    // If Paypal client could not be obtained.
    if (!$paypal) {
      drupal_set_message($this->t('Social Auth Paypal not configured properly. Contact site administrator.'), 'error');
      return $this->redirect('user.login');
    }

    $state = $this->dataHandler->get('oauth2state');

    // Retreives $_GET['state'].
    $retrievedState = $this->request->getCurrentRequest()->query->get('state');
    if (empty($retrievedState) || ($retrievedState !== $state)) {
      $this->userManager->nullifySessionKeys();
      drupal_set_message($this->t('Paypal login failed. Unvalid OAuth2 State.'), 'error');
      return $this->redirect('user.login');
    }

    // Saves access token to session.
    $this->dataHandler->set('access_token', $this->paypalManager->getAccessToken());

    $this->paypalManager->setClient($paypal)->authenticate();

    // Gets user's info from Paypal API.
    /* @var \Stevenmaguire\OAuth2\Client\Provider\PaypalResourceOwner $paypal_profile */
    if (!$paypal_profile = $this->paypalManager->getUserInfo()) {
      drupal_set_message($this->t('Paypal login failed, could not load Paypal profile. Contact site administrator.'), 'error');
      return $this->redirect('user.login');
    }

    // Store the data mapped with data points define is
    // social_auth_paypal settings.
    $data = [];

    $api_calls = explode(',', $this->paypalManager->getAPICalls());

    // Iterate through api calls define in settings and try to retrieve them.
    foreach ($api_calls as $api_call) {
      $call = $this->paypalManager->getExtraDetails($api_call);
      array_push($data, $call);
    }
    // If user information could be retrieved.
    return $this->userManager->authenticateUser($paypal_profile->getName(), $paypal_profile->getEmail(), $paypal_profile->getId(), $this->paypalManager->getAccessToken());

  }

}
