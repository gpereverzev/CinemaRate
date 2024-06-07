<?php
require_once 'E:/СМП/cinemaRate/backend/Database.php';

class Toplist
{
    private $list_id;
    private $user_id;

    public function __construct($list_id, $user_id)
    {
        $this->list_id = $list_id;
        $this->user_id = $user_id;
    }

    public function getListId()
    {
        return $this->list_id;
    }

    public static function createToplist($user_id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('INSERT INTO Toplists (user_id) VALUES (?)');
    if ($stmt->execute([$user_id])) {
        return $db->lastInsertId();
    } else {
        return false;
    }
}

    public static function getUserToplist($user_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM Toplists WHERE user_id = ?');
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getToplist($list_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            'SELECT Films.film_id, Films.title, ListItems.position 
             FROM ListItems 
             JOIN Films ON ListItems.film_id = Films.film_id 
             WHERE ListItems.list_id = ? 
             ORDER BY ListItems.position ASC'
        );
        $stmt->execute([$list_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
