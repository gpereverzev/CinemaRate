<?php
require_once 'Database.php';

class ListItem
{
    private $film_id;
    private $list_id;
    private $position;

    public static function updateFilmPosition($film_id, $new_position)
    {
        // Підключаємося до бази даних
        $db = Database::getInstance()->getConnection();

        // Підготовлюємо SQL-запит для оновлення позиції фільму
        $stmt = $db->prepare('UPDATE ListItems SET position = ? WHERE film_id = ?');

        // Виконуємо запит, передаючи нову позицію та ID фільму
        return $stmt->execute([$new_position, $film_id]);
    }

    public static function addFilmToList($list_id, $film_id, $position)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO ListItems (list_id, film_id, position) VALUES (?, ?, ?)');
        return $stmt->execute([$list_id, $film_id, $position]);
    }

    public static function deleteFilm($film_id)
    {
        // Підключаємося до бази даних
        $db = Database::getInstance()->getConnection();

        // Підготовлюємо SQL-запит для видалення фільму зі списку
        $stmt = $db->prepare('DELETE FROM ListItems WHERE film_id = ?');

        // Виконуємо запит, передаючи ID фільму
        return $stmt->execute([$film_id]);
    }

    public static function getMaxPosition($list_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT MAX(position) AS max_position FROM ListItems WHERE list_id = ?');
        $stmt->execute([$list_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['max_position'] : 0;
    }
}
