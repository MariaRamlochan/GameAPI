<?php

class DeveloperModel extends BaseModel {

    /**
     * A model class for the `developer` database table.
     * It exposes operations that can be performed on developers records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all developers from the `developer` table.
     * @return array A list of developers. 
     */
    public function getAll() {
        $sql = "SELECT * FROM developer";
        $data = $this->rows($sql);
        return $data;
    }

        /**
     * Get a list of Developers whose name matches or contains the provided value.       
     * @param string $name 
     * @return array An array containing the matches found.
     */
    public function getWhereLike($name) {
        $sql = "SELECT * FROM developer WHERE name LIKE :name";
        $data = $this->run($sql, [":name" => $name . "%"])->fetchAll();
        return $data;
    }

    /**
     * Retrieve an developer by its id.
     * @param int $developer_id the id of the developer.
     * @return array an array containing information about a given developer.
     */
    public function getDevelopersById($developer_id) {
        $sql = "SELECT * FROM developer WHERE developer_id = ?";
        $data = $this->run($sql, [$developer_id])->fetch();
        return $data;
    }

    /**
     * Retrieve a list of developers of a given game.
     * @param int $game_id the id of the game.
     * @return array a list of developers.
     */
    public function getDevelopersByGameId($game_id) {
        $sql = "SELECT * FROM developer WHERE game_id = ?";
        $data = $this->run($sql, [$developer_id])->fetchAll();
        return $data;
    }
}
