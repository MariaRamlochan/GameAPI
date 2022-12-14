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
        //$data = $this->rows($sql);
        $data = $this->paginate($sql);
        return $data;
    }

    /**
     * Retrieve a Streamer by its id.
     * @param int $streamer_id the id of the streamer.
     * @return array an array containing information about a given streamer.
     */
    public function getStreamerById($streamer_id) {
        $sql = "SELECT * FROM streamer WHERE streamer_id = ?";
        $data = $this->run($sql, [$streamer_id])->fetch();
        return $data;
    }

    /**
     * Get a list of twitch streamers whos name matches or contains the provided value.       
     * @param string $streamer_name 
     * @return array An array containing the matches found.
     */
    public function getStreamerByName($streamer_name) {
        $sql = "SELECT * FROM streamer WHERE streamer_name LIKE :streamer_name";
        $data = $this->paginate($sql, [":streamer_name" => "%" . $streamer_name . "%"]);
        return $data;
    }

    /**
     * Get a list of twitch streamer by whose game matches the provided value.       
     * @param string $streamed_id
 
     * @return array An array containing the matches found.
     */
    public function getStreamerByPlayedGames($streamed_id) {
        $sql = "SELECT * FROM streamer WHERE streamed_id LIKE :streamed_id";
        $data = $this->paginate($sql, [":streamed_id" => $streamed_id . "%"]);
        return $data;
    }

    /**
     * Create a list of Streamers
     */
    public function createStreamers($data) {
        $data = $this->insert("streamer", $data);
        return $data;
    }

    /**
     * Update a list of Streamers
     */
    public function updateStreamers($data, $where) {
        $data = $this->update("streamer", $data, $where);
        return $data;
    }

    /**
     * Delete a list of Streamers
     */
    public function deleteStreamers($where) {
        $data = $this->delete("streamer", $where);
        return $data;
    }
}
