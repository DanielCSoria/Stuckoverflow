<?php

require_once "framework/Model.php";
require_once "model/User.php";
require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "model/Vote.php";



abstract class Message extends Model {
    private $author; 
    private $body;
    private $timestamp;
    private $post_id; 
    
    public function __construct($author, $body, $post_id, $timestamp){
        $this->author = $author;
        $this->body = $body;
        $this->timestamp = $timestamp;
        $this->post_id = $post_id;
    }

    public function get_id(){ return $this->post_id; }

    public function get_author(){ return $this->author; }

    public function formatted_body(){ 
        $Parsedown = new Parsedown();
        $Parsedown->setSafeMode(true);
        return $Parsedown->text($this->body);
   }
   public function get_body(){ return $this->body; }

    public function get_timestamp(){ return $this->timestamp; }

    abstract function update_db();
    abstract function insert_db();
    abstract function delete();
    abstract function already_exists();
  
    

    public function get_time_info(){
        $old_date = $this->timestamp;
        $old_date = strtotime($old_date);
        $current_time = time();
        $time_diff = $current_time - $old_date;
        $minutes = round($time_diff / 60);
        $hours = round($time_diff / 3600);
        $days = round($time_diff / 86400);
        $months = round($time_diff / 2600640);
        return $this->get_time_message($months, $days, $hours, $minutes);
    }

    protected  function get_time_message($months, $days, $hours, $minutes){
        $msg = "";
        if ($months >= 1) {
            $msg = $msg . "$months months ago";
        } else if ($days >= 1) {
            $msg = $msg . "$days days ago";
        } else if ($hours >= 1) {
            $msg = $msg . "$hours hrs ago";
        } else if ($minutes >= 1) {
            $msg = $msg . "$minutes minutes ago";
        } else {
            $msg = $msg . "just now ";
        }
        return $msg . " by " . "<a class='special_link' href=''>" . $this->author->get_full_name() . "</a>";
    }
    public function can_be_deleted($user){
        return $user && ( $user->is_admin() || $user == $this->author );
    }
    public function can_be_edited($user){
        return $user &&  ( $user->is_admin() || $user == $this->author );
    }
    public function edit($body, $title){
        $this->body = $body;
    }
    public function validate(){
        $errors = [];
        if (!(isset($this->author))) {
            $errors[] = "Incorrect user";
        }
        if (!(isset($this->body) && is_string($this->body) && strlen(str_replace(' ', '', $this->body)) > 0)) {
            $errors[] = "Body must be filled";
        }
        return $errors;
    }
    public function update(){
        if ($this->already_exists()) {
            return $this->update_db();
        } else {
            return $this->insert_db();
        }
    }

    

}
