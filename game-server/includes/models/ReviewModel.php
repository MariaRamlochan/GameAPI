<?php

class ReviewModel extends BaseModel {

    /**
     * A model class for the `review` database table.
     * It exposes operations that can be performed on reviews records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all reviews from the `review` table.
     * @return array A list of reviews. 
     */
    public function getAll() {
        $sql = "SELECT * FROM review";
        //$data = $this->rows($sql);
        $data = $this->paginate($sql);
        return $data;
    }

        /**
     * Get a list of Reviews whose name matches or contains the provided value.       
     * @param string $name 
     * @return array An array containing the matches found.
     */
    public function getWhereLike($rating) {
        $sql = "SELECT * FROM review WHERE rating LIKE :rating";
        $data = $this->paginate($sql, [":rating" => $rating . "%"]);
        return $data;
    }

    /**
     * Retrieve an Reviews by its id.
     * @param int $author_id the id of the review.
     * @return array an array containing information about a given review.
     */
    public function getReviewById($review_id) {
        $sql = "SELECT * FROM review WHERE review_id = ?";
        $data = $this->run($sql, [$review_id])->fetch();
        return $data;
    }

    /**
     * Retrieve a list of reviews from a given author.
     * @param int $author_id the id of the author.
     * @return array a list of reviews.
     */
    public function getReviewsByAuthorID($author_id) {
        $sql = "SELECT * FROM review WHERE author_id = ?";
        $data = $this->paginate($sql, [$author_id]);
        return $data;
    }

    /**
     * Retrieve a list of reviews from a given game.
     * @param int $game_id the id of the game.
     * @return array a list of reviews.
     */
    public function getReviewsByGameID($game_id) {
        $sql = "SELECT * FROM review WHERE game_id = ?";
        $data = $this->paginate($sql, [$game_id]);
        return $data;
    }

     /**
     * Retrieve a list of reviews from a given game.
     * @param int $game_id the id of the game.
     * @return array a list of reviews.
     */
    public function getReviewsByGameIdAndAuthorId($game_id, $author_id) {
        $sql = "SELECT * FROM review WHERE game_id = ? AND author_id = ?";
        $data = $this->paginate($sql, [$game_id, $author_id]);
        return $data;
    }

    /**
     * Create a list of reviews
     */
    public function createReviews($data) {
        $data = $this->insert("review", $data);
        return $data;
    }

    /**
     * Update a list of reviews
     */
    public function updateReviews($data, $where) {
        $data = $this->update("review", $data, $where);
        return $data;
    }

    /**
     * Delete a list of reviews
     */
    public function deleteReviews($where) {
        $data = $this->delete("review", $where);
        return $data;
    }
}
