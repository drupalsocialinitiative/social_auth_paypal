<?php

namespace Drupal\social_auth_paypal\Controller;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\social_api\Plugin\NetworkManager;
use Drupal\social_auth\Controller\OAuth2ControllerBase;
use Drupal\social_auth\SocialAuthDataHandler;
use Drupal\social_auth\User\UserAuthenticator;
use Drupal\social_auth_paypal\PayPalAuthManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Returns responses for Social Auth PayPal module routes.
 */
class PayPalAuthController extends OAuth2ControllerBase {

  /**
   * GoogleAuthController constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\social_api\Plugin\NetworkManager $network_manager
   *   Used to get an instance of social_auth_google network plugin.
   * @param \Drupal\social_auth\User\UserAuthenticator $user_authenticator
   *   Used to manage user authentication/registration.
   * @param \Drupal\social_auth_paypal\PayPalAuthManager $paypal_manager
   *   Used to manage authentication methods.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   Used to access GET parameters.
   * @param \Drupal\social_auth\SocialAuthDataHandler $data_handler
   *   The Social Auth data handler.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Used to handle metadata for redirection to authentication URL.
   */
  public function __construct(MessengerInterface $messenger,
                              NetworkManager $network_manager,
                              UserAuthenticator $user_authenticator,
                              PayPalAuthManager $paypal_manager,
                              RequestStack $request,
                              SocialAuthDataHandler $data_handler,
                              RendererInterface $renderer) {

    parent::__construct('Social Auth PayPal', 'social_auth_paypal',
      $messenger, $network_manager, $user_authenticator,
      $paypal_manager, $request, $data_handler, $renderer);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('plugin.network.manager'),
      $container->get('social_auth.user_authenticator'),
      $container->get('social_auth_paypal.manager'),
      $container->get('request_stack'),
      $container->get('social_auth.data_handler'),
      $container->get('renderer')
    );
  }

  /**
   * Response for path 'user/login/paypal/callback'.
   *
   * PayPal returns the user here after user has authenticated in PayPal.
   */
  public function callback() {
    // Checks if authentication failed.
    if ($this->request->getCurrentRequest()->query->has('error')) {
      $this->messenger->addError($this->t('You could not be authenticated.'));

      return $this->redirect('user.login');
    }

    /** @var \Stevenmaguire\OAuth2\Client\Provider\PayPalResourceOwner|null $profile */
    $profile = $this->processCallback();

    // If authentication was successful.
    if ($profile !== NULL) {

      return $this->userAuthenticator->authenticateUser($profile->getName(),
                                                        $profile->getEmail(),
                                                        $profile->getId(),
                                                        $this->providerManager->getAccessToken());
    }

    return $this->redirect('user.login');
  }

}
