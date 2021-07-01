<?php

require_once "framework/Model.php";
require_once "model/User.php";
require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "model/Vote.php";
require_once "model/Question.php";
require_once "model/Message.php";
require_once "model/Comment.php";


abstract class Post extends Message {
    public function __construct($author, $body, $post_id = NULL, $timestamp = NULL){
        parent::__construct($author,$body,$post_id,$timestamp);
    }
    abstract function is_a_question();
    protected function get_time_message($months, $days, $hours, $minutes){
        $msg = $this->is_a_question() ? "Asked " : "Posted  ";
        return $msg . parent::get_time_message($months,$days,$hours,$minutes);
    }

    public function delete(){ //delete post and all related votes
        self::execute('DELETE FROM comment WHERE PostId = :postid',array("postid"=>$this->get_id()));
        self::execute('DELETE FROM vote WHERE PostId = :post_id', array('post_id' =>  $this->get_id()));
        self::execute('DELETE FROM post WHERE PostId = :post_id', array('post_id' =>  $this->get_id()));
        return $this;
    }

    public function already_exists(){
        $query = self::execute("select * from post where PostId = :id", array("id" => $this->get_id()));
        return $query->rowCount() != 0;
    }
    public function is_upvoted_by($user){
        return $user && Vote::is_post_upvoted($user->get_id(),  $this->get_id());
    }
    public function is_downvoted_by($user){
        return $user && Vote::is_post_downvoted($user->get_id(),  $this->get_id());
    }
 
    public function get_vote_count(){
        return Vote::get_vote_count( $this->get_id());
    }
    public static function get($id){
        $query = self::execute("select * from post where PostId = :id order by Timestamp DESC", array("id" => $id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        }
        if ($data['ParentId'] != NULL)
            return new Answer(User::get_user_by_id($data['AuthorId']), $data['Body'], $data['ParentId'], $data['PostId'], $data['Timestamp']);
        return new Question(User::get_user_by_id($data['AuthorId']), $data['Body'], $data['Title'], $data['AcceptedAnswerId'], $data['PostId'], $data['Timestamp']);
    }
    public function get_comments(){
        $query = self::execute("select* from comment where PostId=:id ORDER BY TIMESTAMP", array("id"=>$this->get_id()));
        $data = $query->fetchAll();
        $comments = [];
        foreach ($data as $row) { 
            $comments[] = new Comment(User::get_user_by_id($row['UserId']), $row['Body'], $row['PostId'], $row['CommentId'], $row['Timestamp'] );
        }
        return $comments;
    }
    public static function get_posts(){
        $query = self::execute("select* from post where PostId=:id ORDER BY TIMESTAMP", array("id"=>$this->get_id()));
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) { 
            $posts[] = new Question(User::get_user_by_id($row['AuthorId']), $row['Body'], $row['Title'], $row['AcceptedAnswerId'],$row['PostId'],$row['Timestamp'] );
        }
        return $posts;
    }


    public function can_be_deleted($user){
        if($user && $user->is_admin())
            return true;
        $query = self::execute('select * from post where PostId in (select PostId from comment where PostId=:id)', array("id" => $this->get_id()));
        if ($query->rowCount() != 0) {
            return false;
        }
        return parent::can_be_deleted($user);
    }

    public function get_comments_as_json($user){
        $comments = $this->get_comments();
        $str ="";
        foreach($comments as $comment){
            $author = json_encode($comment->get_author()->get_full_name());
            $parent_id = json_encode($comment->get_parrent_id());
            $time = json_encode($comment->get_time_info());
            $id = json_encode($comment->get_id());
            $body = json_encode($comment->get_body());
            $canBeUpdated = json_encode($comment->can_be_deleted($user));
            $str .= "{\"id\":$id,\"time\":$time,\"author\":$author,\"parent_id\":$parent_id,\"body\":$body,\"editable\":$canBeUpdated},"; 
        }
        if($str !== "")
            $str = substr($str,0,strlen($str)-1);
        return "[$str]";
    }

   
    

    

   
    

}
