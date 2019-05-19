<?php

namespace Drupal\social_auth_paypal\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\social_auth\Form\SocialAuthSettingsForm;

/**
 * Settings form for Social Auth Paypal.
 */
class PaypalAuthSettingsForm extends SocialAuthSettingsForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_auth_paypal_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array_merge(
      parent::getEditableConfigNames(),
      ['social_auth_paypal.settings']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('social_auth_paypal.settings');

    $is_sandbox = $config->get('is_sandbox') ? 'true' : 'false';

    $form['paypal_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Paypal Client settings'),
      '#open' => TRUE,
      '#description' => $this->t('You need to first create a Paypal App at <a href="@paypal-dev">@paypal-dev</a>', ['@paypal-dev' => 'https://developer.paypal.com/developer/applications/create']),
    ];

    $form['paypal_settings']['client_id'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Client ID'),
      '#default_value' => $config->get('client_id'),
      '#description' => $this->t('Copy the Client ID here.'),
    ];

    $form['paypal_settings']['client_secret'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Client Secret'),
      '#default_value' => $config->get('client_secret'),
      '#description' => $this->t('Copy the Client Secret here.'),
    ];

    $form['paypal_settings']['return_url'] = [
      '#type' => 'textfield',
      '#disabled' => TRUE,
      '#title' => $this->t('Return URL'),
      '#description' => $this->t('Copy this value to <em>Return URL</em> field of your Paypal App settings.'),
      '#default_value' => Url::fromRoute('social_auth_paypal.callback')->setAbsolute()->toString(),
    ];

    $form['paypal_settings']['is_sandbox'] = [
      '#type' => 'radios',
      '#title' => $this->t('Is this a sandbox?'),
      '#default_value' => $is_sandbox,
      '#required' => TRUE,
      '#options' => [
        'true' => $this->t('Yes'),
        'false' => $this->t('No'),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $is_sandbox = $values['is_sandbox'] == 'true' ? TRUE : FALSE;

    $this->config('social_auth_paypal.settings')
      ->set('client_id', $values['client_id'])
      ->set('client_secret', $values['client_secret'])
      ->set('is_sandbox', $is_sandbox)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
