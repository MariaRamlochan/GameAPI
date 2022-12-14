<?php

class streamModel extends BaseModel {

    /**
     * A model class for the `stream` database table.
     * It exposes operations that can be performed on stream records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all mobile games from the `stream` table.
     * @return array A list of twitch streams. 
     */
    public function getAll() {
        $sql = "SELECT * FROM stream";
        //$data = $this->rows($sql);
        $data = $this->paginate($sql);
        return $data;
    }

    /**
     * Retrieve a stream by its id.
     * @param int $stream_id the id of the stream.
     * @return array an array containing information about a given stream.
     */
    public function getStreamById($stream_id) {
        $sql = "SELECT * FROM stream WHERE stream_id = ?";
        $data = $this->run($sql, [$stream_id])->fetch();
        return $data;
    }

    /**
     * Get a list of Streams whose title matches or contains the provided value.       
     * @param string $title 
     * @return array An array containing the matches found.
     */
    public function getStreamByTitle($title) {
        $sql = "SELECT * FROM stream WHERE title LIKE :title";
        $data = $this->paginate($sql, [":title" => "%" . $title . "%"]);
        return $data;
    }

    /**
     * Get a list of streamed games where the game name matches or contains the provided value.       
     * @param string $game_id 
     * @return array An array containing the matches found.
     */
    public function getStreamedGameByGameId($game_id) {
        $sql = "SELECT * FROM stream WHERE game_id LIKE :game_id";
        $data = $this->paginate($sql, [":game_id" => "%" . $game_id . "%"]);
        return $data;
    }

    /**
     * Get a list of twitch streamed games by where game matches the provided value.       
     * @param string $streamer_id
 
     * @return array An array containing the matches found.
     */
    public function getStreamedGamebyStreamerId($streamer_id) {
        $sql = "SELECT * FROM stream WHERE streamer_id LIKE :streamer_id"; 
        $data = $this->paginate($sql, [":streamer_id" => $streamer_id . "%"]);
        return $data;
    }

    /**
     * Create a list of streamed game
     */
    public function createStreams($data) {
        $data = $this->insert("stream", $data);
        return $data;
    }

    /**
     * Update a list of streamed game
     */
    public function updateStreams($data, $where) {
        $data = $this->update("stream", $data, $where);
        return $data;
    }

    /**
     * Delete a list of streamed game
     */
    public function deleteStreams($where) {
        $data = $this->delete("stream", $where);
        return $data;
    }
}
