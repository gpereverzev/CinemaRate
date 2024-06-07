<?php

class Comment {
    private $coment_id;
    private $user_id;
    private $film_id;
    private $text;

    public function __construct($coment_id, $user_id, $film_id, $text) {
        $this->coment_id = $coment_id;
        $this->user_id = $user_id;
        $this->film_id = $film_id;
        $this->text = $text;
    }
}

?>