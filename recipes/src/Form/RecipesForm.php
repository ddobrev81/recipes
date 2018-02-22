<?php

namespace Drupal\recipes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\recipes\RecipesStorage;

/**
 * Recipes add form.
 */
class RecipesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'recipe_add';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => TRUE,
    ];
    $form['author'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Author name'),
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Author email'),
      '#required' => TRUE,
      '#description' => $this->t('Please enter your email.'),
      '#element_validate' => ['recipes_validate_email'],
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Recipe description'),
      '#required' => TRUE,
      '#maxlength' => 500,
    ];
    $form['instructions'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Recipe instructions'),
      '#required' => TRUE,
    ];
    $form['ingredients'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Recipe ingredients'),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit recipe'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Prepare the form values for saving.
    $language = \Drupal::languageManager()->getCurrentLanguage()->getName();
    $timestamp = \Drupal::time()->getCurrentTime();;

    $entry = [
      'language' => $language,
      'created' => $timestamp,
      'title' => $form_state->getValue('title'),
      'author' => $form_state->getValue('author'),
      'email' => $form_state->getValue('email'),
      'description' => $form_state->getValue('description'),
      'instructions' => $form_state->getValue('instructions'),
      'ingredients' => $form_state->getValue('ingredients'),
    ];

    $result = RecipesStorage::create($entry);
    if ($result) {
      drupal_set_message($this->t('Success'), 'status');
      // Send confirmation email.
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'recipes';
      $key = 'recipe_notify';
      $to = \Drupal::config('recipe_email_config.settings')->get('email');
      $params['message'] = Url::fromRoute('recipes.view', ['id' => $result])->toString();
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = TRUE;
      $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
      // Redirect to front page.
      $form_state->setRedirect('<front>');
    }
    else {
      $form_state->setRebuild();
    }
  }

}
