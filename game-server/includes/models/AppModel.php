<?php

class AppModel extends BaseModel {

    /**
     * A model class for the `app` database table.
     * It exposes operations that can be performed on games records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all mobile games from the `app` table.
     * @return array A list of mobile games. 
     */
    public function getAll() {
        $sql = "SELECT * FROM app";
        $data = $this->rows($sql);
        return $data;
    }

    /**
     * Get a list of mobile games whose title matches or contains the provided value.       
     * @param string $app_name 
     * @return array An array containing the matches found.
     */
    public function getMobileGameByName($app_name) {
        $sql = "SELECT * FROM app WHERE app_name LIKE :app_name";
        $data = $this->run($sql, [":app_name" => $app_name . "%"])->fetchAll();
        return $data;
    }

    /**
     * Get a list of Mobile Mobile Game whose category matches the provided value.       
     * @param string $app_category 
     * @return array An array containing the matches found.
     */
    public function getMobileGamesByCategory($app_category) {
        $sql = "SELECT * FROM app WHERE app_category LIKE :app_category";
        $data = $this->run($sql, [":app_category" => $app_category . "%"])->fetchAll();
        return $data;
    }

    /**
     * Get the number of download for a specific Mobile Game     
     * @param string $num_downloads 
     * @return array An array containing the matches found.
     */
    public function getMobileGamesByNumberOfDownloads($num_downloads) {
        $sql = "SELECT * FROM app WHERE num_downloads LIKE :num_downloads";
        $data = $this->run($sql, [":num_downloads" => $num_downloads . "%"])->fetchAll();
        return $data;
    }

    /**
     * Get a list of Mobile Game whose developer matches the provided value.       
     * @param string $developer 
     * @return array An array containing the matches found.
     */
    public function getMobileGamesByDeveloper($developer) {
        $sql = "SELECT * FROM app WHERE developer LIKE :developer";
        $data = $this->run($sql, [":developer" => $developer . "%"])->fetchAll();
        return $data;
    }

    /**
     * Retrieve an Mobile Game by its id.
     * @param int $app_id the id of the app.
     * @return array an array containing information about a given app.
     */
    public function getMobileGameById($app_id) {
        $sql = "SELECT * FROM app WHERE app_id = ?";
        $data = $this->run($sql, [$app_id])->fetch();
        return $data;
    }

    /**
     * Create a list of Mobile Game
     */
    public function createMobileGames($data) {
        $data = $this->insert("app", $data);
        return $data;
    }

    /**
     * Update a list of Mobile Game
     */
    public function updateMobileGames($data, $where) {
        $data = $this->update("app", $data, $where);
        return $data;
    }

    /**
     * Delete a list of Mobile Game
     */
    public function deleteMobileGames($where) {
        $data = $this->delete("app", $where);
        return $data;
    }
}
