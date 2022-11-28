<?php

class streamedModel extends BaseModel {

    /**
     * A model class for the `streamed` database table.
     * It exposes operations that can be performed on games records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all mobile games from the `streamed` table.
     * @return array A list of twitch streameds. 
     */
    public function getAll() {
        $sql = "SELECT * FROM streamer_game";
        $data = $this->rows($sql);
        return $data;
    }

    /**
     * Retrieve a streamed by its id.
     * @param int $streamed_id the id of the streamed.
     * @return array an array containing information about a given streamed.
     */
    public function getStreamedGameById($streamed_id) {
        $sql = "SELECT * FROM streamer_game WHERE streamed_id = ?";
        $data = $this->run($sql, [$streamed_id])->fetch();
        return $data;
    }

    /**
     * Get a list of streamed games where the game name matches or contains the provided value.       
     * @param string $game_id 
     * @return array An array containing the matches found.
     */
    public function getStreamedGameByGameId($game_id) {
        $sql = "SELECT * FROM streamer_game WHERE game_id LIKE :game_id";
        $data = $this->run($sql, [":game_id" => "%" . $game_id . "%"])->fetchAll();
        return $data;
    }

    /**
     * Get a list of twitch streamed games by where game matches the provided value.       
     * @param string $streamer_id
 
     * @return array An array containing the matches found.
     */
    public function getStreamedGamebyStreamerId($streamer_id) {
        $sql = "SELECT * FROM streamer_game WHERE streamer_id LIKE :streamer_id"; 
        $data = $this->run($sql, [":streamer_id" => $streamer_id . "%"])->fetchAll();
        return $data;
    }

    /**
     * Create a list of streamed game
     */
    public function createStreamed($data) {
        $data = $this->insert("streamer_game", $data);
        return $data;
    }

    /**
     * Update a list of streamed game
     */
    public function updateStreamed($data, $where) {
        $data = $this->update("streamer_game", $data, $where);
        return $data;
    }

    /**
     * Delete a list of streamed game
     */
    public function deleteStreamed($where) {
        $data = $this->delete("streamer_game", $where);
        return $data;
    }
}
