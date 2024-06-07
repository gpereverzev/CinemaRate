<?php

class Rate {
    private $rate_id;
    private $film_id;
    private $user_id;
    private $rate;

    public function __construct($rate_id, $film_id, $user_id, $rate) {
        $this->rate_id = $rate_id;
        $this->film_id = $film_id;
        $this->user_id = $user_id;
        $this->rate = $rate;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public static function getRatesAndCommentsByFilmId($film_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            'SELECT Users.user_id, Users.username, Rates.rate, Coments.text 
             FROM Rates 
             JOIN Users ON Rates.user_id = Users.user_id 
             LEFT JOIN Coments ON Rates.user_id = Coments.user_id AND Rates.film_id = Coments.film_id 
             WHERE Rates.film_id = ?'
        );
        $stmt->execute([$film_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addRate($film_id, $user_id, $rate) {
        $db = Database::getInstance()->getConnection();
        
        // Check if rate already exists
        $stmt = $db->prepare('SELECT rate_id FROM Rates WHERE user_id = ? AND film_id = ?');
        $stmt->execute([$user_id, $film_id]);
        
        if ($stmt->fetch()) {
            // Update existing rate
            $stmt = $db->prepare('UPDATE Rates SET rate = ? WHERE user_id = ? AND film_id = ?');
            return $stmt->execute([$rate, $user_id, $film_id]);
        } else {
            // Add new rate
            $stmt = $db->prepare('INSERT INTO Rates (film_id, user_id, rate) VALUES (?, ?, ?)');
            return $stmt->execute([$film_id, $user_id, $rate]);
        }
    }
}
?>
