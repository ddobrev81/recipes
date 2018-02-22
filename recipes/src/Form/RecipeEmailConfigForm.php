<?php

namespace Drupal\recipes\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class RecipeEmailConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'recipe_email_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'recipe_email_config.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('recipe_email_config.settings');

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Configure email address to be notified of recipe submission.'),
      '#required' => TRUE,
      '#description' => "Please enter your email.",
      '#element_validate' => ['recipes_validate_email'],
      '#default_value' => $config->get('email'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration and save the setting.
    $this->configFactory->getEditable('recipe_email_config.settings')
      ->set('email', $form_state->getValue('email'))
      ->save();
    drupal_set_message($this->t('Email saved.'), 'status');

  }

}
