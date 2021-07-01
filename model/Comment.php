<?php

require_once "framework/Model.php";
require_once "model/User.php";
require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "model/Vote.php";
require_once "model/Question.php";
require_once "model/Message.php";

class Comment extends Message {
    private $parrent_id;

    public function __construct($author, $body,$parrent_id, $post_id = NULL, $timestamp = NULL){
        parent::__construct($author,$body,$post_id,$timestamp);
        $this->parrent_id=$parrent_id;
    }
    public function get_parrent_id() { return $this->parrent_id; }

    protected function get_time_message($months, $days, $hours, $minutes){
        return "Commented " . parent::get_time_message($months,$days,$hours,$minutes);
    }
    public function update_db(){
        parent::execute("UPDATE COMMENT SET Body=:body, Timestamp = CURRENT_TIMESTAMP where CommentId = :id", array("body" => $this->get_body(), "id" => $this->get_id()));
    }
    public function insert_db(){
        parent::execute("INSERT INTO COMMENT (UserId, Body,PostId) VALUES (:author,:body,:id)", array("author" => $this->get_author()->get_id(), "body" => $this->get_body(), "id" => $this->parrent_id));
    }
    public function delete(){ //delete post and all related votes
        self::execute('DELETE FROM comment WHERE CommentId = :id', array('id' =>  $this->get_id()));
        return $this;
    }
    public function already_exists(){
        $query = self::execute("select * from comment where CommentId = :id", array("id" => $this->get_id()));
        $data = $query->fetch();
        return $query->rowCount() != 0;
    }
     
    public static function get($id){
        $query = self::execute("select * from comment where CommentId = :id order by Timestamp DESC", array("id" => $id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        }
        return new Comment(User::get_user_by_id($data['UserId']), $data['Body'], $data['PostId'], $data['CommentId'], $data['Timestamp'] );
    }

    
    
}
