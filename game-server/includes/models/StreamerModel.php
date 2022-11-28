<?php

class StreamerModel extends BaseModel {

    /**
     * A model class for the `streamer` database table.
     * It exposes operations that can be performed on games records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all mobile games from the `streamer` table.
     * @return array A list of twitch streamers. 
     */
    public function getAll() {
        $sql = "SELECT * FROM streamer";
        $data = $this->rows($sql);
        return $data;
    }

    /**
     * Get a list of twitch streamers whos name matches or contains the provided value.       
     * @param string $streamer_name 
     * @return array An array containing the matches found.
     */
    public function getStreamerByName($streamer_name) {
        $sql = "SELECT * FROM streamer WHERE streamer_name LIKE :streamer_name";
        $data = $this->run($sql, [":streamer_name" => "%" . $streamer_name . "%"])->fetchAll();
        return $data;
    }
f
    /**
     * Get a list of twitch streamer by whose game matches the provided value.       
     * @param string $streamed_id
 
     * @return array An array containing the matches found.
     */
    public function getStreamerByPlayedGames($streamed_id) {
        $sql = "SELECT * FROM streamer WHERE streamed_id LIKE :streamed_id";
        $data = $this->run($sql, [":streamed_id" => $streamed_id . "%"])->fetchAll();
        return $data;
    }

    /**
     * Create a list of Streamers
     */
    public function createStreamer($data) {
        $data = $this->insert("streamer", $data);
        return $data;
    }

    /**
     * Update a list of Streamers
     */
    public function updateStreamer($data, $where) {
        $data = $this->update("streamer", $data, $where);
        return $data;
    }

    /**
     * Delete a list of Streamers
     */
    public function deleteStreamer($where) {
        $data = $this->delete("streamer", $where);
        return $data;
    }
}
