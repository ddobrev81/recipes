<?php

namespace Drupal\recipes\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\recipes\RecipesStorage;

/**
 * Controller that handles the recipes view pages.
 */
class RecipesController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content($id) {
    $recipe = RecipesStorage::read($id);
    // var_dump($recipe);exit;
    if ($recipe) {
      $build = [
        '#theme' => 'recipes_view',
        '#created' => (int) $recipe[0]->created,
        '#title' => $recipe[0]->title,
        '#author' => $recipe[0]->author,
        '#email' => $recipe[0]->email,
        '#description' => $recipe[0]->description,
        '#instructions' => $recipe[0]->instructions,
        '#ingredients' => $recipe[0]->ingredients,
      ];
    }
    return $build;
  }

}
