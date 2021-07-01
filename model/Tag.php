<?php

require_once "framework/Model.php";

require_once "lib/parsedown-1.7.3/Parsedown.php";


class Tag extends Model {
    private $tag_name;
    private $post_id;
    private $tag_id;

    public function __construct($tag_name, $tag_id = null, $post_id = null) {
        $this->tag_name = $tag_name;
        $this->post_id = $post_id;
        $this->tag_id = $tag_id;
    }
    public function get_post_id(){ return $this->post_id; }
    public function get_id() { return $this->tag_id; }
    public function get_tag_name() { return $this->tag_name; }
    public function count_related_posts($id) {
        $query = self::execute("SELECT count(*) total from posttag where TagId = :id", array("id"=>$id));
        $data = $query->fetch();
        return  $data['total'];
    }
    public static function get_tags(){
        $query = self::execute("select * from tag ", array());
        $data = $query->fetchALL();
        $tags = [];
        foreach($data as $row){
           $tags[] = new Tag($row['TagName'], $row['TagId']);
       }
       return $tags;
    }
    private function insert(){
        self::execute("INSERT INTO tag (TagName) VALUES (:tagname)", array("tagname" => $this->tag_name));
    }
    public function delete(){ 
        self::execute('DELETE FROM posttag WHERE TagId = :id', array("id"=>$this->tag_id));
        self::execute('DELETE FROM tag WHERE TagId = :id',array("id"=>$this->tag_id));
    }
    public function unlink_tag(){
        self::execute('DELETE FROM posttag WHERE TagId = :id', array("id"=>$this->tag_id));
    }
    public function link_tag($id){
        self::execute("INSERT INTO posttag (PostId, TagId) VALUES (:postid, :tagid)", 
        array("postid" => $id, "tagid"=> $this->tag_id));
    }
    private function update_tag(){
        self::execute("UPDATE tag SET TagName =:tagname where TagId = :id", array("tagname" => $this->tag_name, "id" => $this->tag_id));
    }
    
    private function tag_exists(){
        $query = self::execute("select * from tag where TagId = :id", array("id" => $this->tag_id));
        return $query->rowCount() !=0;
    }

    public static function check_name($tag_name){
        $query = self::execute("select * from tag where TagName = :id", array("id" => $tag_name));
        return $query->rowCount() !=0;
    }  
    private function tag_name_exists(){
       return Tag::check_name($this->get_tag_name());
    }  
    
    public function validate(){
        $errors = [];
        if (!(isset($this->tag_name) && is_string($this->tag_name) && strlen(str_replace(' ', '', $this->tag_name)) > 0)) {
            $errors[] = "You must give a name";
        }
        elseif(strlen($this->tag_name) > 15) { 
            $errors[] = "Tag name too long, should be under 15 characters";
         }
         elseif($this->tag_name_exists()) {
             $errors[] = "Tagname already exist";
         }
        
        return $errors;
    }
   
    public function update(){
        if ($this->tag_exists()) {
            $this->update_tag();
        } else {
            $this->insert();
        }
    }
    public static function get_by_id($tagid){
        $query = self::execute("select * from tag where TagId = :tagid", array("tagid" => $tagid));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        }
        return new Tag($data['TagName'], $data['TagId']);
    }

    public function can_be_deleted($user){
        return $user && $user->is_admin();
    }

    public function edit($tagname){
        $this->tag_name = $tagname;
    }

    public static function get_available_tags($post)
    {
        $rows = self::execute("SELECT * from tag where tagid NOT IN(select tagid from posttag where postid=:postid) order by tagname asc", array("postid"=>$post->get_id()));
        $tags = [];
        foreach ($rows as $row) {
            $tags[] = new Tag($row['TagName'], $row['TagId']);
        }
        return $tags;
    }
    


    
    








}
























?>