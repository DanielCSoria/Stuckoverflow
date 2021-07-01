<?php

require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "framework/Tools.php";
require_once "model/Comment.php";

/* for views which require two steps, one in get the other in post, we're looking for the id wether it has been passed in post or get, then we find the related comment.
if there is a comment and user can operate, we check wether it's a submit or a first ask.*/

class ControllerComment extends Controller
{
    public function index()
    {
        $this->redirect();
    }

    public function add()
    {
        $user = parent::get_user_or_redirect();
        $errors = [];
        if (isset($_GET["param1"]) || isset($_POST["post_id"])) {
            $id = isset($_GET["param1"]) ? $_GET["param1"] : $_POST["post_id"];
            $post = Post::get($id);
            if ($post) {
                $errors = $this->manage_add($user, $post);
                //its the first time they ask or they failed
                (new View("add"))->show(array("user" => $user, "post" => $post, "errors" => $errors));
                return;
            }
        }
        //no or wrong id
        $this->redirect();
    }

    public function edit()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_GET['param1']) || isset($_POST['id'])) {
            $comment_id = isset($_GET['param1']) ? $_GET['param1'] : $_POST['id'];
            $comment = Comment::get($comment_id);
            if ($comment && $comment->can_be_edited($user)) {
                $post = Post::get($comment->get_parrent_id());
                $errors = $this->manage_edit($comment);
                (new View("comment_edit"))->show(array("user" => $user, "comment" => $comment, "post" => $post, "errors" => $errors));
                return;
            }
        }
        //wrong params or wrong user
        $this->redirect();
    }
    
    private function redirect_after_comment($post)
    {
        if ($post->is_a_question()) {
            $this->redirect("post", "show", $post->get_id());
        }
        $this->redirect("post", "show", $post->get_parrent_id());
    }

    public function delete()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) || isset($_POST['id'])) {
            $comment_id = isset($_GET['param1']) ? $_GET['param1'] : $_POST['id'];
            $comment = Comment::get($comment_id);
            if ($comment && $comment->can_be_deleted($user)) {
                $this->manage_delete($user,$comment);
                (new View("confirm_delete"))->show(array("user" => $user, "item" => $comment,"target_type"=>"comment"));
                return;
            }
        }
        $this->redirect("post", "index");
    }

    private function manage_delete($user,$comment)
    {
        if (isset($_POST["confirm"]) && $_POST['confirm'] != "") {
            $comment = $comment->can_be_deleted($user) ? $comment->delete() : false;
        }
        if(isset($_POST["confirm"]) || isset($_POST["cancel"]))
            $this->redirect_after_comment(Post::get($comment->get_parrent_id()));
    }

    private function manage_add($user, $post)
    {
        $errors = [];
        if (isset($_POST['comment'])) { // its a submit
            $comment = new Comment($user, $_POST['comment'], $post->get_id());
            $errors = $comment->validate();
            if (empty($errors)) {
                $comment->update();
                $this->redirect_after_comment($post);
            }
        }
        return $errors;
    }

    private function manage_edit($comment)
    {
        $errors = [];
        if (isset($_POST["id"]) && isset($_POST["comment"])) {
            $comment->edit($_POST['comment'], "");
            $errors = $comment->validate();
            if (empty($errors)) {
                $comment->update();
                $this->redirect_after_comment(Post::get($comment->get_parrent_id()));
            }
        }
        return $errors;
    }

    public function create_service(){
        if(isset($_POST["post_id"]) && isset($_POST["body"])){
            $user = $this->get_user_or_false();
            $post = Post::get($_POST["post_id"]);
            if($post && $user){
                $comment = new Comment($user, $_POST["body"], $post->get_id());
                $errors = $comment->validate();
                if (empty($errors)) {
                    $comment->update();
                    echo "true";
                    return;
                 }
            }
         echo "false";
        }       
    }

    public function edit_service(){
        $user = $this->get_user_or_false();
        if (isset($_POST["comment_id"]) && isset($_POST["body"]) && $user) {
            $comment = Comment::get($_POST["comment_id"]);
            if($comment && $comment->can_be_edited($user)){
                $comment->edit($_POST["body"],"");
                $errors = $comment->validate();
                if(empty($errors)){
                    $comment->update();
                    echo "true";
                    return;
                }
            }
        }
        echo "false";
    }


    public function delete_service(){
        $user = $this->get_user_or_false();
        if(isset($_POST["comment_id"])){
            $comment = Comment::get($_POST["comment_id"]);
            if($comment && $comment->can_be_deleted($user)){
                $comment->delete();
                echo "true";
                return;
            }
        }
        echo "false";
    }

}
