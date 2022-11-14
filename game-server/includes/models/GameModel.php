<?php

class GameModel extends BaseModel {

    /**
     * A model class for the `game` database table.
     * It exposes operations that can be performed on games records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all games from the `game` table.
     * @return array A list of games. 
     */
    public function getAll() {
        $sql = "SELECT * FROM game";
        $data = $this->rows($sql);
        return $data;
    }

        /**
     * Get a list of Games whose title matches or contains the provided value.       
     * @param string $title 
     * @return array An array containing the matches found.
     */
    public function getWhereLike($title) {
        $sql = "SELECT * FROM game WHERE Title LIKE :title";
        $data = $this->run($sql, [":title" => $title . "%"])->fetchAll();
        return $data;
    }

    /**
     * Retrieve an Gamne by its id.
     * @param int $game_id the id of the game.
     * @return array an array containing information about a given game.
     */
    public function getGameById($game_id) {
        $sql = "SELECT * FROM game WHERE GameId = ?";
        $data = $this->run($sql, [$game_id])->fetch();
        return $data;
    }

    /**
     * Retrieve a list of games of a given developer.
     * @param int $game_id the id of the developer.
     * @return array a list of games.
     */
    public function getGameByDeveloperId($game_id) {
        $sql = "SELECT * FROM game WHERE DeveloperID = ?";
        $data = $this->run($sql, [$game_id])->fetchAll();
        return $data;
    }

    /**
     * Retrieve a list of games of a given publisher.
     * @param int $game_id the id of the publisher.
     * @return array a list of games.
     */
    public function getGameByPublisherId($game_id) {
        $sql = "SELECT * FROM game WHERE PublisherID = ?";
        $data = $this->run($sql, [$game_id])->fetchAll();
        return $data;
    }

}
