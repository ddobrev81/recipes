<?php

namespace Drupal\recipes;

use Drupal\Core\Database\Database;

/**
 * CRUD implementation for RecipesStorage.
 */
class RecipesStorage {

  /**
   * Save an entry in the database.
   *
   * @param array $entry
   *   Data to save to the database.
   *
   * @return \Drupal\Core\Database\StatementInterface|int|null
   *   Result of the query.
   *
   * @throws \Exception
   *   When the database insert fails.
   */
  public static function create(array $entry) {
    $result = NULL;
    try {
      $connection = Database::getConnection();
      $result = $connection->insert('recipes')->fields($entry)->execute();
    }
    catch (\Exception $e) {
      drupal_set_message(t('Database write failed. Message = %message, query= %query', [
        '%message' => $e->getMessage(),
        '%query' => $e->query_string,
      ]
      ), 'error');
    }
    return $result;
  }

  /**
   * Read from the database using an recipe id.
   *
   * @param int $id
   *   Id of the recipe.
   *
   * @return object
   *   An object containing the loaded entries if found.
   */
  public static function read($id) {
    $connection = Database::getConnection();
    $query = $connection->query("SELECT * FROM {recipes} WHERE id = :id", [':id' => $id]);
    $result = $query->fetchAll();
    return $result;
  }

}
