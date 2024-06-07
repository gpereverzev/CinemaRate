<?php

class Film {
    private $film_id;
    private $title;
    private $genre;
    private $anotation;
    private $director;
    private $actors;
    private $poster;

    public function __construct($film_id, $title, $genre, $anotation, $director, $actors, $poster) {
        $this->film_id = $film_id;
        $this->title = $title;
        $this->genre = $genre;
        $this->anotation = $anotation;
        $this->director = $director;
        $this->actors = $actors;
        $this->poster = $poster;
    }

    public static function getAllFilms() {
        $db = new PDO('sqlite:E:/СМП/cinemaRate/src/cinemaRate.db');
        $statement = $db->prepare("SELECT * FROM Films");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getFilmById($film_id) {
        $db = new PDO('sqlite:E:/СМП/cinemaRate/src/cinemaRate.db');
        $statement = $db->prepare("SELECT * FROM Films WHERE film_id = :film_id");
        $statement->bindParam(':film_id', $film_id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}
?>
