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
    public function getGameByTitle($title) {
        $sql = "SELECT * FROM game WHERE title LIKE :title";
        $data = $this->run($sql, [":title" => $title . "%"])->fetchAll();
        return $data;
    }

    /**
     * Get a list of Games whose genre matches the provided value.       
     * @param string $genre 
     * @return array An array containing the matches found.
     */
    public function getGamesByGenre($genre) {
        $sql = "SELECT * FROM game WHERE genre LIKE :genre";
        $data = $this->run($sql, [":genre" => $genre . "%"])->fetchAll();
        return $data;
    }

    /**
     * Get a list of Games whose platform matches the provided value.       
     * @param string $platform 
     * @return array An array containing the matches found.
     */
    public function getGamesByPlatform($platform) {
        $sql = "SELECT * FROM game WHERE platform LIKE :platform";
        $data = $this->run($sql, [":platform" => $platform . "%"])->fetchAll();
        return $data;
    }

    /**
     * Get a list of Games whose publisher matches the provided value.       
     * @param string $publisher 
     * @return array An array containing the matches found.
     */
    public function getGamesByPublisher($publisher) {
        $sql = "SELECT * FROM game WHERE publisher LIKE :publisher";
        $data = $this->run($sql, [":publisher" => $publisher . "%"])->fetchAll();
        return $data;
    }

    /**
     * Get a list of Games whose developer matches the provided value.       
     * @param string $developer 
     * @return array An array containing the matches found.
     */
    public function getGamesByDeveloper($developer) {
        $sql = "SELECT * FROM game WHERE developer LIKE :platform";
        $data = $this->run($sql, [":developer" => $developer . "%"])->fetchAll();
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
     * Create a list of games
     */
    public function createGames($data) {
        $data = $this->insert("game", $data);
        return $data;
    }

    /**
     * Update a list of games
     */
    public function updateGames($data, $where) {
        $data = $this->update("game", $data, $where);
        return $data;
    }

    /**
     * Delete a list of games
     */
    public function deleteGames($where) {
        $data = $this->delete("game", $where);
        return $data;
    }
}
