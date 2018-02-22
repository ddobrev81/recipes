<?php

namespace Drupal\recipes\Tests;

use Drupal\Core\Form\FormState;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\simpletest\WebTestBase;

/**
 * A class for testing the recipe add form.
 */
class RecipesFormTest extends WebTestBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The full class of the form being tested.
   *
   * @var string
   */
  protected $formClass;

  /**
   * Form arguments.
   *
   * @var array
   */
  protected $formArgs = ['id' => NULL, 'dialog' => FALSE];

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'recipes',
  ];

  /**
   * Setup function for our test class.
   */
  protected function setUp() {
    parent::setUp();
    $web_user = $this
      ->drupalCreateUser([
        'recipe_add',
      ]);
    $this
      ->drupalLogin($web_user);

    // Add a language.
    ConfigurableLanguage::createFromLangcode('en')
      ->save();
  }

  /**
   * Return a new instance of the form being tested.
   *
   * @return \Drupal\Core\Form\ConfigFormBase
   *   Return the form derivative.
   */
  protected function getFormInstance() {
    $class = $this->formClass;
    return $class::create($this->container);
  }

  /**
   * Retrieve a new formstate instance.
   *
   * @return \Drupal\Core\Form\FormStateInterface
   *   Return the form state.
   */
  protected function getFormStateInstance() {
    return new FormState();
  }

  /**
   * Verify that the form contains all fields we require.
   */
  public function testFieldExistence() {
    $this->drupalGet('recipes.form_add');
    $fields = [
      'title' => 'Title',
      'author' => 'Author',
      'email' => 'author@example.com',
      'description' => 'description',
      'instructions' => 'instructions',
      'ingredients' => 'ingredients',
    ];
    foreach ($fields as $field => $default_value) {
      $this->assertFieldById($field, $default_value);
    }
  }

  /**
   * Test posting data to the recipe add form.
   */
  public function testFormSubmit() {
    // Assert that all (simple) fields submit as intended.
    $edit = [
      'title' => 'Title',
      'author' => 'Author',
      'email' => 'author@example.com',
      'description' => 'description',
      'instructions' => 'instructions',
      'ingredients' => 'ingredients',
    ];
    $this->drupalPostForm('recipes.form_add', $edit, t('Submit recipe'));
    $this->drupalGet('recipes.form_add');
    foreach ($edit as $field => $value) {
      $this->assertFieldById($field, $value);
    }
    // Assert headers behavior.
    $form = $this->getFormInstance();
    $form_state = $this->getFormStateInstance();
    $form_state->addBuildInfo('args', [$this->formArgs]);
    $form_state->setValue('headers', [['field' => 'foo', 'value' => 'bar']]);
    $this->formBuilder->submitForm($form, $form_state);
    $this->assertEqual(0, count($form_state->getErrors()));
  }

}
