<?php

require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once 'model/Message.php';
require_once 'model/Post.php';
require_once 'model/Answer.php';

class ControllerUser extends Controller {

    public function signup() {

        $name = '';
        $password = '';
        $password_confirm = '';
        $errors = [];
        $full_name = '';
        $email = '';

        if (isset($_POST['signupPseudo']) && isset($_POST['signupPassword']) && isset($_POST['signupPasswordConfirm']) && isset($_POST['signupName']) && isset($_POST['signupEmail'])) {
            $name = trim($_POST['signupPseudo']);
            $password = $_POST['signupPassword'];
            $password_confirm = $_POST['signupPasswordConfirm'];
            $email = $_POST['signupEmail'];
            $full_name = $_POST['signupName'];
            $user = new User($name, Tools::my_hash($password), $email, $full_name);
            $errors = $this-> check_signup($user,$email,$password,$password_confirm);
            if (count($errors) == 0) {
                $user->update(); 
                parent::log_user($user);
            }
        }
        (new View("signup"))->show(array("password" => $password, "password_confirm" => $password_confirm, "name" => $name, "errors" => $errors, "full_name" => $full_name, "email" => $email, "user" => $this->get_user_or_false()));
    }

    private function check_signup($user,$email,$password,$password_confirm){
        $errors = $user->validate();
        $errors = array_merge($errors, User::validate_mail_format($email));
        $errors = array_merge($errors, $user->validate_unicity());
        $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
        return $errors;
    }

    public function login() {
        $errors=[];
        $name = '';
        $password = '';
        if (isset($_POST['pseudo']) && isset($_POST['password'])) {
            $name = $_POST['pseudo'];
            $password = $_POST['password'];

            $errors = User::validate_login($name,$password);
            if (empty($errors)) {
                parent::log_user(User::get_user_by_name($name));
            }
        }
        (new View("login"))->show(array("name" => $name, "password" => $password, "errors" => $errors, "user" => $this->get_user_or_false()));
    }

    public function stats(){
        (new View("stats"))->show(array("user"=>$this->get_user_or_false()));
    }

    public function logout_confirm() {
        if (isset($_POST["confirm"])) {
            $confirm = $_POST["confirm"];
            if ($confirm == "accept") {
                $this->logout();
            }
            $this->redirect();
        }
        (new View("logout_confirm"))->show(array("user" => $this->get_user_or_false()));
    }

    public function index() {
        $this->signup();
    }

    public function vote() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST['vote']) && isset($_POST['id'])) {
            $post = Post::get($_POST['id']);
            $vote_type = $_POST['vote'];
            if ($post) {
                $this->manage_vote($user, $post,$vote_type);
                $this->redirect_after_vote($post);
            }
        }
        $this->redirect_after_vote($post);
    }

    //accept or unaccept actually
    public function accept(){
        $user = $this->get_user_or_false();
        if(!$user)
            $this->redirect("user","login");
        if((isset($_POST['accept']) || isset($_POST['unaccept']))){
            $id = isset($_POST['accept']) ? $_POST['accept'] : $_POST['unaccept'];
            $post = Post::get($id);
            if($post){
                if(isset($_POST["accept"]))
                     $this->accept_answer($user,$post);
                else
                    $this->unaccept_answer($user,$post);
                $this->redirect_after_vote($post);
            }
        }
        $this->redirect("post","index");
    }

    private function manage_vote($user, $post,$vote_type) {
        $res = true;
       if($vote_type == "upvote")
            $user->up_vote($post);
        elseif ($vote_type == "downvote")
            $user->down_vote($post);
        else
           $res = false;
        return $res;
    }

    private function accept_answer($user, $post) {
        if (!$post->is_a_question()) {
            if ($post->can_be_accepted($user)) 
                $post = $post->confirm_answer(); 
            return $post;
        }
        return false;
    }
    private function unaccept_answer($user,$post){
        if(!$post->is_a_question()){
            if ($post->can_be_unaccepted($user))
                $post->unconfirm_answer();
            return $post;
        }
        return false;
    }

    private function redirect_after_vote($post){
        if (!$post->is_a_question()) {
            $this->redirect("post", "show", $post->get_parrent_id());
        } else {
            $this->redirect("post", "show", $post->get_id());
        }
    }

    //ajax services 

    public function pseudo_available_service(){
        $res = "true";
        if(isset($_POST["pseudo"]) && $_POST["pseudo"] !== ""){
            $member = User::get_user_by_name($_POST["pseudo"]);
            if($member)
                $res = "false";
        }
        echo $res;
    }

    public static function mail_available_service(){
        if(isset($_POST["signupEmail"])){
            $answer = User::is_mail_available($_POST["signupEmail"]);
            echo $answer;

        }
    }

    //return 5 more actives users for a given time period (ex : 50 days = 50*1 , 50 months =  50*30 days (approximatively but still))
    public function get_activity_as_json(){
        if(isset($_GET["param1"]) && $_GET["param1"] != "" && isset($_GET["param2"])&& isset($_GET["param2"]) != "")
            echo User::get_activity_as_json($_GET["param1"],$_GET["param2"]);

    }

    //return actions related to user activity for given time period
    public function get_recent_activity(){
        if(isset($_GET["param1"]) && $_GET["param1"] != "" && isset($_GET["param2"])&& isset($_GET["param2"]) != "" && isset($_GET["param3"]) && $_GET["param3"] != "")
            echo User::get_recent_activity($_GET["param1"],$_GET["param2"],$_GET["param3"]);

    }

    //ensure that user can vote (as we did before (same function)) and return wether vote has been accepted or not to the view
    public function vote_service(){
        $user = $this->get_user_or_false();
        if(isset($_POST["post_id"]) && isset($_POST["action"]) && $user){
            $post = Post::get($_POST["post_id"]);
            $action = $_POST["action"];
            if($post){
                $res = $this->manage_vote($user,$post,$action);
                if($res){
                    echo "true";
                    return;
                }
            }
        }
        echo "false";
    }
    //return needed informations after update (upvote : true, downvote : false, count : 0) as ex.
    public function vote_status_service(){
        $user=$this->get_user_or_false();
        $up = false;
        $down = false;
        $count = 0;
        $str = '';
        if(isset($_POST["post_id"])){
            $post = Post::get($_POST["post_id"]);
            if($post){
                $up = json_encode($post->is_upvoted_by($user));
                $down = json_encode($post->is_downvoted_by($user));
                $count = json_encode($post->get_vote_count());
                $str .= "{\"up\":$up,\"down\":$down,\"count\":$count},";  
            } 
        }
        if($str !== "")
            $str = substr($str,0,strlen($str)-1);
        echo "[$str]";
    }

    public function login_service(){
        if(isset($_POST["pseudo"]) && isset($_POST["password"])){
            $errors = User::validate_login($_POST["pseudo"],$_POST["password"]);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_name($_POST["pseudo"]));
                echo "true";
                return;
            }
        }
        echo "false";
    }

    protected function log_user($user, $controller = "", $action = "index"){
        $_SESSION["user"] = $user;
        session_write_close();
    }

    //must override logout and login method to prevent from redirect :x (basic behavior implemented in controller)
    public function logout(){
        session_destroy();
    }

    public function log_out_service(){
        $this->logout();
        echo "true";
    }

    public function signup_service(){
        $password = '';
        $password_confirm = '';
        $email = '';

        if (isset($_POST['signupPseudo']) && isset($_POST['signupPassword']) && isset($_POST['signupPasswordConfirm']) && isset($_POST['signupName']) && isset($_POST['signupEmail'])) {
            $password = $_POST['signupPassword'];
            $password_confirm = $_POST['signupPasswordConfirm'];
            $email = $_POST['signupEmail'];
            $user = new User(trim($_POST['signupPseudo']), Tools::my_hash($password), $_POST['signupEmail'],  $_POST['signupName']);
            $errors = $this-> check_signup($user,$email,$password,$password_confirm);
            if (count($errors) == 0) {
                $user->update(); 
                self::log_user($user);
                echo "true";
                return;
            }
        }
        echo "false";
    }
    

}
