<?php

class PlatformModel extends BaseModel {

    /**
     * A model class for the `platform` database table.
     * It exposes operations that can be performed on platforms records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all platforms from the `platform` table.
     * @return array A list of platforms. 
     */
    public function getAll() {
        $sql = "SELECT * FROM platform";
        $data = $this->rows($sql);
        return $data;
    }

        /**
     * Get a list of Platforms whose title matches or contains the provided value.       
     * @param string $title 
     * @return array An array containing the matches found.
     */
    public function getWhereLike($title) {
        $sql = "SELECT * FROM platform WHERE Title LIKE :title";
        $data = $this->run($sql, [":title" => $title . "%"])->fetchAll();
        return $data;
    }

    /**
     * Retrieve an Pubvlishers by its id.
     * @param int $platform_id the id of the platform.
     * @return array an array containing information about a given platform.
     */
    public function getPlatformsById($platform_id) {
        $sql = "SELECT * FROM platform WHERE PlatformId = ?";
        $data = $this->run($sql, [$platform_id])->fetch();
        return $data;
    }

    /**
     * Retrieve a list of platforms of a given platform.
     * @param int $platform_id the id of the platform.
     * @return array a list of platforms.
     */
    public function getPlatformyGameId($platform_id) {
        $sql = "SELECT * FROM platform WHERE GameID = ?";
        $data = $this->run($sql, [$platform_id])->fetchAll();
        return $data;
    }
}
