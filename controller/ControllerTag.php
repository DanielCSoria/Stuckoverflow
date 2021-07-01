<?php

require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "framework/Tools.php";
require_once "model/Tag.php";
require_once "model/Question.php";
require_once "model/User.php";


class ControllerTag extends Controller {

    public function index() {
        $this->add();
    }

    public function add() {
        $user = $this->get_user_or_false();
        $errors = [];
        $tags = Tag::get_tags();
        if(isset($_POST['newtag'])){
            $errors = $this->manage_insert($_POST['newtag']);
            if (count($errors) == 0){
                $this->redirect("tag");
            }
        } 
        (new View("tag"))->show(array("tags"=>$tags, "user"=>$user,"errors"=>$errors));
    }

    //create and save tag in db if everything is ok
    private function manage_insert($tag_name) {
        $errors = [];
        $tag = new Tag($tag_name);
        $errors = $tag->validate();
        if (empty($errors)) {
            $tag->update();
        } 
        return $errors;
    }

    public function delete(){
        $user = $this->get_user_or_redirect();
        if(!$user->is_admin())
            throw new Exception("Should never happen");
        if(isset($_GET["param1"]) && $_GET['param1'] != ""){
            $tag = Tag::get_by_id($_GET['param1']);
            if($tag){
                (new View("confirm_delete", $tag->get_id()))->show(array("user" => $this->get_user_or_false(), "item" => $tag,"target_type"=>"tag"));
                return;
            }
        }
        elseif (isset($_POST["confirm"]) || isset($_POST['cancel'])) {
            $this->confirm_delete($user);
        } 
        $this->redirect("tag");        
    }

    private function confirm_delete($user) { 
        $tag = isset($_POST['confirm']) ? $_POST['confirm'] : $_POST['cancel'];
        $tag = Tag::get_by_id($tag);
        if (isset($_POST["confirm"]) && $_POST['confirm'] != "") {
            $tag = $tag->can_be_deleted($user) ? $tag->delete() : false;
        }
    }

    //remove link between tag and question
    public function unlink_tag(){
        $user = $this->get_user_or_redirect();
        if(isset($_POST["tag_id"]) && isset($_POST["post_id"])){
            $tag = Tag::get_by_id(($_POST["tag_id"]));
            $post = Post::get($_POST["post_id"]);
            if(!$post || !$tag)
                $this->redirect("post","show",$post->get_id());
            if(!$post->can_be_edited($user))
                throw new Exception("Should never happen : wrong user :O");
            $tag->unlink_tag();
        }
        $this->redirect("post","show",$post->get_id());
    }

    //add link between question and tag
    public function link_post(){
        $user = $this->get_user_or_redirect();
        if(isset($_POST["tag_id"]) && isset($_POST["post_id"])){
            $tag = Tag::get_by_id(($_POST["tag_id"]));
            $post = Post::get($_POST["post_id"]);
            if(!$post || !$tag)
                $this->redirect();
            if(!$post->can_add_tag($user))
                throw new Exception("Should never happen : max tags reached or wrong user :O");
            $tag->link_tag($post->get_id());
            $this->redirect("post","show",$post->get_id());
        }
    }
    
    
    public function edit(){
        $user = $this->get_user_or_redirect();
        if(!$user->is_admin())
            throw new Exception("Should never happen.. User isnt admin :O");
        $errors = [];
        $tags = Tag::get_tags();
        if(isset($_POST['id']) && isset($_POST['edit_tag'])){
            $tag = Tag::get_by_id($_POST['id']);
            if($tag){
                $tag->edit($_POST['edit_tag']);
                $errors = $tag->validate();
                if(empty($errors)){
                    $tag->update();
                    $this->redirect("tag");
                }     
            }
            (new View("tag"))->show(array("tags"=>$tags, "user"=>$user,"errors"=>$errors));   
        }   
    }
    public function manage_edit($tag, $errors, $user, $tags){
        $bodytag = $_POST['edit_tag'];
        $tag->edit($bodytag);
        $errors = $tag->validate();
         if(empty($errors)){
             $tag->update();
             $this->redirect("tag");
         }
         (new View("tag"))->show(array("tags"=>$tags, "user"=>$user,"errors"=>$errors)); 
    }

    public function tag_name_exists_service(){
        $res = "true";
        if(isset($_POST["newtag"]) && $_POST["newtag"] !=  ""){
            if(Tag::check_name($_POST["newtag"]))
                 $res = "false";
        }
        echo $res;
       
    }

}


