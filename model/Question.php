<?php

require_once "framework/Model.php";
require_once "model/User.php";
require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "model/Vote.php";
require_once "model/Answer.php";
require_once "model/Post.php";
require_once "model/Tag.php";


class Question extends Post{
    private $title;
    private $accepted_answer_id;
    private static $NEWEST = "select * from post where ParentId is null order by Timestamp DESC";
    private static $VOTE = 'SELECT post.*, max_score
    FROM post, ( SELECT parentid, max(score) max_score FROM ( SELECT post.postid, ifnull(post.parentid, post.postid) parentid, ifnull(sum(vote.updown), 0) score
            FROM post LEFT JOIN vote ON vote.postid = post.postid
            GROUP BY post.postid
        ) AS tbl1
        GROUP by parentid
    ) AS q1
    WHERE post.postid = q1.parentid ORDER BY q1.max_score DESC, timestamp DESC';
    private static $UNANSWERED = "select * from post where ParentId is null and PostId not in 
    (select a.PostId from post a join post b on a.PostId = b.ParentId) order by timestamp desc";
    private static $SEARCH = "select * from post join user on AuthorId = UserId where Title LIKE :id
    OR (UserName LIKE :id AND ParentId is null) OR PostId in 
    (select ParentId from post join user on AuthorId=UserId where (title is null  or title = '') and (body LIKE :id OR UserName LIKE :id))";
    private static $BYTAG = "SELECT * FROM post a join posttag b on a.PostId= b.PostId where b.TagId =:id order by Timestamp DESC";
    private static $ACTIVES = "select question.PostId, question.AuthorId, question.Title, question.Body, question.ParentId, question.Timestamp, question.AcceptedAnswerId 
    from post as question, 
         (select post_updates.postId, max(post_updates.timestamp) as timestamp from (
            select q.postId as postId, q.timestamp from post q where q.parentId is null
            UNION
            select a.parentId as postId, a.timestamp from post a where a.parentId is not null
            UNION
            select c.postId as postId, c.timestamp from comment c 
            UNION 
            select a.parentId as postId, c.timestamp 
            from post a, comment c 
            WHERE c.postId = a.postId and a.parentId is not null
            ) as post_updates
          group by post_updates.postId) as last_post_update
    where question.postId = last_post_update.postId and question.parentId is null
    order by last_post_update.timestamp DESC";

    public function __construct($author, $body, $title, $accepted_answer_id = NULL, $post_id = NULL, $timestamp = NULL){
        parent::__construct($author, $body, $post_id, $timestamp);
        $this->title = ucfirst($title);
        $this->accepted_answer_id = $accepted_answer_id;
    }
    public function get_title(){ return $this->title; }

    public function get_accepted(){ return $this->accepted_answer_id; }
    
    public function validate(){
        $errors = parent::validate();
        if (!(isset($this->title) && is_string($this->title) &&strlen(str_replace(' ', '', $this->title)) > 0)) {
            $errors[] = "You must give a title";
        } elseif (strlen($this->title) >= 60) {
            $errors[] = "Title length must be under 60 chars";
        }
        return $errors;
    }
    public function is_a_question(){
        return true;
    }
    public function update_db(){
        self::execute("UPDATE POST SET Title=:title, Body=:body,Timestamp = CURRENT_TIMESTAMP where PostId = :id", array("title" => $this->title, "body" => $this->get_body(), "id" => $this->get_id()));
        return $this->get_id();
    }
    public function insert_db(){
        self::execute("INSERT INTO post (AuthorId, Title, Body,ParentId) VALUES (:author,:title,:body, :id)", array("title" => $this->title, "body" => $this->get_body(), "author" => $this->get_author()->get_id(), "id" => $this->get_id()));
        return $this->lastInsertId();
    }
    public function get_answers()
    {   //if no row found in vote, then return 0 instead of null (like sum() would have done) : thats why we used coalesce
        $query = self::execute('select * from post p where p.ParentId = :id
                        order by (select count(*) from post where p.PostId = AcceptedAnswerId) DESC
                        ,(select COALESCE(sum(UpDown),0) from vote where PostId = p.PostId) DESC, Timestamp DESC', array("id" => $this->get_id()));
        $data = $query->fetchAll();
        $answers = [];
        foreach ($data as $row) {
            $answers[] = new Answer(User::get_user_by_id($row['AuthorId']), $row['Body'], $row['ParentId'], $row['PostId'], $row['Timestamp']);
        }
        return $answers;
    }
    //check if this question is related to an answer , if not calls super else return false
    public function can_be_deleted($user){
        if($user && $user->is_admin())
            return true;
        $query = self::execute('SELECT * from post where ParentId = :id', array("id" => $this->get_id()));
        if ($query->rowCount() != 0) {
            return false;
        }
        return parent::can_be_deleted($user);
    }
    
    public static function count_questions(){
        $query = self::execute("SELECT count(*) as count FROM post where ParentId is  null",array());
        $data = $query->fetch();
        if($query-> rowCount() == 0){
            return 0;
        }
        return $data['count'];

    }
    public static function count_unanswered(){
        $query = self::execute("select count(*) as count from post where ParentId is null and PostId not in 
        (select a.PostId from post a join post b on a.PostId = b.ParentId)",array());
        $data = $query->fetch();
        if($query-> rowCount() == 0){
            return 0;
        }
        return $data['count'];

    }
    //an option is given to this function, request are already defined since they are constant.
    public static function get_questions($option,$page, $array = []){
        $nbPost = Configuration::get("page_posts");
        $limit = " LIMIT " . ($page-1)*$nbPost . ", " . $nbPost;
        $request = Question::$NEWEST;
        switch ($option) {
            case ("search"):
                $request = Question::$SEARCH;
                break;
            case ("unanswered"):
                $request = Question::$UNANSWERED;
                break;
            case ("vote"):
                $request = Question::$VOTE;
                break;
            case ("tag") :
                 $request = Question::$BYTAG;
                 break;   
            case("actives") :
                $request = Question::$ACTIVES;
                break;
        }
        $query = self::execute($request . $limit, $array);
        $data = $query->fetchAll();
        $answers = [];
        foreach ($data as $row) { 
            $answers[] = new Question(User::get_user_by_id($row['AuthorId']), $row['Body'], $row['Title'], $row['AcceptedAnswerId'], $row['PostId'], $row['Timestamp']);
        }
        return [$answers,count(self::execute($request, $array)->fetchAll())];
        //return answers related as an array + count
    }

    public function get_answers_count(){
        $query = self::execute("select * from post where ParentId = :id order by Timestamp DESC", array("id" => $this->get_id()));
        return $query->rowCount();
    }
    public function edit($body, $title){
        $this->title = $title;
        parent::edit($body,$title);
    }

    public function delete(){ //delete answers + tags and call super delete
       foreach ($this->get_answers() as $answer){
           $answer->delete();
       }
       self::execute("DELETE FROM posttag where PostId = :postid",array("postid" => $this->get_id()));
       return parent::delete();
    }

    //return questions tag
    public function get_tags(){
        $query = self::execute("select t.TagId,t.TagName  from posttag p join tag t on t.TagId = p.TagId where p.PostId = :id", array("id"=>$this->get_id()));
        $data = $query->fetchALL();
        $tags = [];
        foreach($data as $row){
           $tags[] = new Tag($row['TagName'], $row['TagId']);
       }
       return $tags;
    }

    public function get_tags_as_json(){
        $tags = $this->get_tags();
        $str ="";
        foreach($tags as $tag){
            $name = json_encode($tag->get_tag_name());
            $id = json_encode($tag->get_id());
            $str .= "{\"id\":$id,\"name\":$name},"; 
        }
        if($str !== "")
            $str = substr($str,0,strlen($str)-1);
        return "[$str]";

    }

    private function count_tags(){
        $query = self::execute("select * from posttag where PostId=:id", array("id"=>$this->get_id()));
        return $query->rowCount();
    }

    public function can_add_tag($user){
        return $this->can_be_edited($user) && $this->count_tags() < Configuration::get("max_tags");
    }
   
   

    public static function get_posts_as_json($option,$page,$array = []){
        $posts = Question::get_questions($option,$page,$array);
        $str ="";
        foreach($posts[0] as $post){
            $vote_count = json_encode($post->get_vote_count());
            $title = json_encode($post->get_title());
            $id = json_encode($post->get_id());
            $time = json_encode($post->get_time_info());
            $answer_count = json_encode($post->get_answers_count());
            $tags_array = $post->get_tags_as_json();
            $str .= "{\"id\":$id,\"title\":$title,\"voteCount\":$vote_count,\"answCount\":$answer_count,\"time\":$time,\"tags\":$tags_array},"; 
        }
        if($str !== "")
            $str = substr($str,0,strlen($str)-1);
        return ["[$str]",$posts[1]];
    }
      
     

  
}
