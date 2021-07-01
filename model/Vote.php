<?php

require_once "framework/Model.php";

class Vote extends Model {
    private $user_id;
    private $post_id;
    private $updown;
    public function __construct($uid,$pid,$upd){
        $this->user_id = $uid;
        $this->post_id = $pid;
        $this->updown = $upd;
    }
    public function validate(){
        $query = self::execute("select UpDown from vote where UserId = :uid and PostId = :pid",array("uid"=>$this->user_id,"pid"=>$this->post_id));
        if($query->rowCount() !== 0){
            $data = $query->fetch();
            return $data['UpDown'];
        }
        return 0;
    }
    private function delete(){
        self::execute("delete from vote where PostId = :pid and UserId = :uid",array("uid"=>$this->user_id,"pid"=>$this->post_id));
    }
    public function update(){
        $status = $this->validate();
        switch($status){
            case($this->updown === 1 ? 1 : -1) : $this-> delete(); 
            break;
            case($this->updown === 1 ? -1 : 1) : $this->delete();
            default :         
            self::execute("insert into vote (UserId,PostId,UpDown) values(:uid,:pid,:ud)",array("uid"=>$this->user_id,"pid"=>$this->post_id,"ud"=>$this->updown));

            }
    }
    public static function get_vote_count($post_id){
        return self::get_down_vote($post_id)+self::get_up_vote($post_id);
    }
    private static function get_down_vote($post_id) {
        $query = self::execute("SELECT count(*) total from vote where PostId = :id and UpDown = -1", array("id" => $post_id));
        $data = $query->fetch();
        return -$data['total'];
    }

    private static function get_up_vote($post_id) {
        $query = self::execute("SELECT count(*) total from vote where PostId = :id and UpDown = 1", array("id" => $post_id));
        $data = $query->fetch();
        return $data['total'];
    }

    public static function is_post_upvoted($user_id, $post_id) {
        $query = self::execute("SELECT * from vote where PostId = :id and UserId = :userid and UpDown = 1", array("userid" => $user_id, "id" => $post_id));
        if ($query->rowCount() > 0)
            return true;
        return false;
    }

    public static function is_post_downvoted($user_id, $post_id) {
        $query = self::execute("SELECT * from vote where PostId = :id and UserId = :userid and UpDown = -1", array("userid" => $user_id, "id" => $post_id));
        if ($query->rowCount() > 0)
            return true;
        return false;
    }

    

}
