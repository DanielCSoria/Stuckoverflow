<?php

require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "framework/Tools.php";
require_once "model/Vote.php";
require_once "controller/Utils.php";
require_once "model/Question.php";
require_once "model/Tag.php";

class ControllerPost extends Controller { 

    //we keep this function in index cause it would be burdensome to update all html references to a new controller function
    public function index() {
        $option="newest";
        $posts = [];
        $page = 1;
        if(isset($_GET["param1"])){
            $option = $_GET["param1"];
            if(isset($_GET["param2"]))
                $page = $_GET["param2"] > 0 ? $_GET["param2"] : 1;
            if($option == "search" || $option == "tag"){
                    $this-> manage_special_views($option,$page);
                    return;
                }      
            }              
        $posts = Question::get_questions($option,$page);
        (new View("index"))->show(array("posts" => $posts[0],"user" => $this->get_user_or_false(),"active"=>$option,"nb_post"=>$posts[1],"page"=>$page));
    }

    //handles by tag and search view which need a bit different call of the view (filter,tag_name)
    private function manage_special_views($option,$page){
        if(!isset($_GET["param3"]))
            $this->redirect("post");
        $tag_name="";
        $filter = $_GET["param3"];
        if($option == "tag"){
            $posts = Question::get_questions($option,$page,array("id" => $_GET["param3"]));
            $tag_name = Tag::get_by_id($_GET["param3"]);
            $tag_name = $tag_name == false ? $this->redirect() : $tag_name->get_tag_name();
        }
        else
            $posts = $this->manage_search($page);

        (new View("index"))->show(array("posts" => $posts[0], "user" => $this->get_user_or_false(),"active"=>$option,"filter"=>$filter,"nb_post"=>$posts[1],"tag_name"=>$tag_name,"page"=>$page));
    }

    public function launch_search(){
        if (isset($_POST['search'])) { 
            $filter = Utils::url_safe_encode($_POST['search']);
            $this->redirect("post","index","search","1",$filter);
        }
    }

    private function get_tags_tab($tags){
        $res = [];
        foreach($tags as $tag)
            $res[] = Tag::get_by_id($tag);
        return $res;
    }


    public function ask() {
        $title = '';
        $errors = [];
        $body = '';
        $tags_array = [];
        if(isset($_POST["tags"]))
            $tags_array = $this->get_tags_tab($_POST["tags"]);
        $tags = Tag::get_tags();
        $user = $this->get_user_or_redirect();
        if (isset($_POST['title']) && isset($_POST['body'])) {
            $title = $_POST['title'];
            $body = $_POST['body'];
            $errors = $this->manage_ask($user,$title,$body);
            if (count($errors) == 0) {
              $this->redirect("post", "index");
            }
        }
        (new View("ask"))->show(array("title" => $title, "tags"=>$tags, "body" => $body, "errors" => $errors, "user" => $user,"selected_tags"=>$tags_array));
    }

    public function delete() {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) || isset($_POST['id'])) {
            $post_id = isset($_GET['param1']) ? $_GET['param1'] : $_POST['id'];
            $post = Post::get($post_id);
            if ($post && $post->can_be_deleted($user)) {
                if(isset($_POST["confirm"]) || isset($_POST["cancel"]))
                    $this->confirm_delete($user,$post);
                (new View("confirm_delete"))->show(array("user" => $user, "item" => $post,"target_type"=>"post"));
                return;
            }
        }
        $this->redirect("post", "index");
    }

    public function edit(){
        $user = $this->get_user_or_redirect();
        $post = false;
        if(isset($_GET["param1"]) || isset($_POST['post_id'])){
            $id = isset($_GET['param1']) ? $_GET['param1'] : $_POST['post_id'];
            $post = Post::get($id);
            $this->manage_edit($post, $user);
        }
        else{
            parent::redirect("post","index");
        }  
    }

    public function show() {
        $answers = [];
        $errors = [];
        $user = $this->get_user_or_false();
        if (isset($_GET["param1"])) {
            $post = Post::get($_GET["param1"]);
            if (isset($_POST['body'])) {
                $errors = $this->manage_answer($user, $post);
            }
            if ($post && $post->is_a_question()) {
                $tags = Tag::get_available_tags($post);
                $answers = $post->get_answers("answers", array("id" => $post->get_id()));
                (new View("show"))->show(array("post" => $post, "answers" => $answers, "errors" => $errors, "user" => $user,"tags"=>$tags));
                return;
            }
        }
        self::redirect("post","index");
        
        
    }
    private function manage_tags($tags,$post_id){
        //link all selected tags to post.
        foreach($tags as $tag){
            $tag = Tag::get_by_id($tag);
            $tag->link_tag($post_id);            
        }
       
    }

    private function manage_ask($user,$title,$body) {
        $max_tags = Configuration::get("max_tags");
        $errors = [];
        $tags = null;
        $post = new Question($user, $body, $title);
        $errors = $post->validate();
        //we need to check wether the tab of tags is superior to max tags here. Cause we need to create the post before adding tags. But we cant create
        //post if we dont know if the number of tags is in the bounds.
        if(isset($_POST["tags"])){
            $tags = $_POST["tags"];
            if(count($tags) > $max_tags)
                 $errors[] = "You cannot add more than " . $max_tags . " tags.";
        }
        if (empty($errors)) {
            $post_id = $post->update();   
            if($tags != null) 
                $this->manage_tags($tags,$post_id);
        }  
        return $errors;
    }

    //if param 3 , do search, if not , exception.
    private function manage_search($page){
        if (isset($_GET["param3"])) {
            $filter = Utils::url_safe_decode($_GET["param3"]);
            if (!$filter)
                Tools::abort("Bad url parameter " . $_GET["param3"]);
            else{
                return Question::get_questions("search", $page, array("id" => "%" . $filter . "%"));
            }
        }
        else{
            throw new Exception();
        }
    }

    private function manage_edit($post, $user){
        $errors = [];
        if(isset($_POST["post_id"])){
            $errors = $this->confirm_edit($post);
            if(empty($errors)){
                $post->update();
                $this->redirect("post","show", !$post->is_a_question()? $post->get_parrent_id() : $post->get_id());
            }
        }
        elseif(!$post){
            parent::redirect("post","index");
        }
        (new View("edit"))->show(array("post" => $post, "errors" => $errors, "user" => $user));
    }
   
    private function manage_answer($user, $post) {
        $errors = [];
        $body = $_POST['body'];
        if ($user) {
            $new_answer = new Answer($user, $body, $post->get_id());
            $errors = $new_answer->validate();
            if (empty($errors)) {
                $new_answer->update();
                $this->redirect("post","show",$new_answer->get_parrent_id());
            }
        } else {
           $this->redirect("user","login");
        }
        return $errors;
    }
    
    private function confirm_delete($user,$post) { 
        if (isset($_POST["confirm"]) && $_POST['confirm'] != "") {
            $post = $post->can_be_deleted($user) ? $post->delete() : false;
        }
        $this->redirect_after_delete($post);
    }

    private function redirect_after_delete($post){
        if (!$post) {
            throw new Exception("Wrong ID / not permitted action");
        } elseif ($post->is_a_question() && isset($_POST['cancel'])) {
            $this->redirect("post", "show",$post->get_id());
        } elseif($post->is_a_question()){
            $this->redirect("post","index");
        }
        else{
            $this->redirect("post", "show", $post->get_parrent_id());
        }
    }
    private function confirm_edit($post){
        $title= "";
        if (isset($_POST['body'])){
            $body = $_POST['body'];
        }
        if(isset($_POST['title'])){
            $title = $_POST['title'];
        }
        $post->edit($body,$title);
        $errors = $post->validate();
        return $errors;
    }
    
    public function get_comments_as_json(){
        if(isset($_GET["param1"]) && $_GET["param1"] != "") {
            $post = Post::get($_GET["param1"]);
            if($post)
                echo $post->get_comments_as_json($this->get_user_or_false());
        }
    }   
    public function get_posts_as_json(){
        if(isset($_GET["param1"]) && isset($_GET["param2"])){
            $option = $_GET["param1"];
            $page = $_GET["param2"];
            $array = [];
            if(isset($_GET["param3"])){
                //if there's a 3d param then it's tag or search, if bad parameter for search returns newests instead
                if($option == "search"){
                  $filter = Utils::url_safe_decode($_GET["param3"]);
                  if($filter)
                    $array = array("id" => "%" . $filter . "%");
                  else{
                    $option = "newest";
                    $page=1;
                  }
                }
                else{
                    $array = array("id" => $_GET["param3"]);
                }
            }
            $res = Question::get_posts_as_json($option,$page,$array);
            //returns posts and total nb of posts for pagination
            echo json_encode(array("posts"=>$res[0],"count"=>$res[1]));

        }
    }
    
}
