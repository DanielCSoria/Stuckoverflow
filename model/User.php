<?php

require_once "framework/Model.php";
class User extends Model
{
    private $user_id;
    private $name;
    private $hashed_password;
    private $email;
    private $full_name;
    private $role;

    public function __construct($name, $hashed_password, $email, $full_name, $id = NULL, $role = "user")
    {
        $this->user_id = $id;
        $this->name = $name;
        $this->hashed_password = $hashed_password;
        $this->email = $email;
        $this->full_name = $full_name;
        $this->role = $role;
    }

    public function get_id()
    {
        return $this->user_id;
    }
    public function get_name()
    {
        return $this->name;
    }
    public function get_email()
    {
        return $this->email;
    }
    public function get_full_name()
    {
        return $this->full_name;
    }
    public function is_admin()
    {
        return $this->role === "admin";
    }


    public function update()
    {
        if (self::get_user_by_name($this->name)) {
            self::execute("UPDATE user SET Password=:password, FullName =:full_name, Email = :email WHERE UserName=:name ", array("password" => $this->hashed_password, "full_name" => $this->full_name, "name" => $this->name, "email" => $this->email));
        } else {
            self::execute("INSERT INTO user(UserName,Password,Email,FullName) VALUES(:name,:password,:email,:full_name)", array("name" => $this->name, "password" => $this->hashed_password, "email" => $this->email, "full_name" => $this->full_name));
        }
        $this->user_id = self::lastInsertId();
        return $this;
    }

    public function validate()
    {
        $errors = array();
        if (!(isset($this->name) && is_string($this->name) && strlen($this->name) > 0)) {
            $errors[] = "User name is required.";
        } elseif (!(isset($this->name) && is_string($this->name) && strlen(str_replace(' ', '', $this->name)) >= 3 && strlen($this->name) <= 20)) {
            $errors[] = "User name length must be between 3 and 16.";
        } elseif (!(isset($this->name) && is_string($this->name) && preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $this->name))) {
            $errors[] = "User name must start by a letter and must contain only letters and numbers.";
        }
        if (strlen(str_replace(' ', '', $this->full_name)) < 3) {
            $errors[] = $this->full_name == "" ? "You must enter a full name" : "Full name length must be > to 3";
        }
        return $errors;
    }

    public static function validate_login($name, $password)
    {
        $errors = [];
        $user = User::get_user_by_name($name);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors[] = "Wrong password, please retry";
            }
        } else {
            $errors[] = "No user with this user name. Please sign up.";
        }
        return $errors;
    }

    private static function get_coeff($period)
    {
        switch ($period) {
            case ("Days"):
                return 1;
            case ("Week"):
                return 7;
            case ("Month"):
                return 30;
            case ("Year"):
                return 364;
        }
    }

    public function validate_unicity()
    {
        $errors = [];
        $query = self::execute("SELECT * FROM user where Email = :email or UserName= :name", array("email" => $this->email, "name" => $this->name));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() != 0) {
            $errors[] = "User name or mail address already taken.";
        }
        return $errors;
    }


    public static function validate_mail_format($email)
    {
        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (ctype_space($email) || empty($email))
                $errors[] = "You must enter an e-mail address.";
            else
                $errors[] = $email . " is an incorrect e-mail address";
        }
        return $errors;
    }

    public static function validate_passwords($password, $password_conf)
    {
        $errors = User::validate_password($password);
        if ($password != $password_conf) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    private static function validate_password($password)
    {
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        }
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:!,.\/?\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    private static function check_password($clear_password, $hash)
    {
        return $hash === Tools::my_hash($clear_password);
    }


    public static function get_user_by_name($name)
    {
        $query = self::execute("SELECT * FROM user where UserName = :name", array("name" => $name));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        }
        return new User($data["UserName"], $data["Password"], $data["Email"], $data["FullName"], $data["UserId"], $data['Role']);
    }

    public static function get_user_by_id($id)
    {
        $query = self::execute("SELECT * FROM user where UserId= :id", array("id" => $id));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        }
        return new User($data["UserName"], $data["Password"], $data["Email"], $data["FullName"], $data["UserId"], $data['Role']);
    }


    public function up_vote($post)
    {
        $v = new Vote($this->user_id, $post->get_id(), 1);
        $v->update();
    }
    public function down_vote($post)
    {
        $v = new Vote($this->user_id, $post->get_id(), -1);
        $v->update();
    }

    public static function get_activity_as_json($days, $period)
    {
        $coeff = self::get_coeff($period);
        $days = $days * $coeff;
        $date_limit = date('Y-m-d', strtotime('- ' . $days . ' days'));
        $limit = "LIMIT " . Configuration::get("nb_users");
        $request = self::execute(" select name,count(*) as time from (select u.UserName as name,u.UserId as userId, p.Timestamp as time from user u join post p on u.UserId = p.AuthorId  where Timestamp >= :date union 
        select u.UserName as name,u.UserId as userId, c.Timestamp as time from user u join comment c on u.UserId=c.UserId where Timestamp >= :date) as t1 group by t1.userId " . $limit, array("date" => $date_limit));
        $request = $request->fetchAll();
        $str = "";
        foreach ($request as $row) {
            $name = json_encode($row["name"]);
            $activity = json_encode($row["time"]);
            $str .= "{\"name\":$name,\"activity\":$activity},";
        }
        if ($str !== "")
            $str = substr($str, 0, strlen($str) - 1);
        return "[$str]";
    }


    public static function get_recent_activity($user, $days, $period)
    {
        $coeff = self::get_coeff($period);
        $days = $days * $coeff;
        $date_limit = date('Y-m-d', strtotime('- ' . $days . ' days'));
        $limit = "LIMIT " . Configuration::get("nb_users");
        $str = "";
        $str .= User::get_questions_activity($limit, $date_limit, $user);
        $str .= User::get_answers_activity($limit, $date_limit, $user);
        $str .= User::get_comment_activity($limit, $date_limit, $user);
        if ($str !== "")
            $str = substr($str, 0, strlen($str) - 1);
        return "[$str]";
    }

    private static function get_questions_activity($limit, $date, $user_name)
    {
        $request = self::execute("select post.Title as title,post.Timestamp as Timestamp from post join user on post.AuthorId = user.UserId where ParentId is null and user.UserName = :name and post.Timestamp >= :date " . $limit, array("date" => $date, "name" => $user_name));
        return User::encode_activity_details($request, "Question create/update");
    }

    private static function get_answers_activity($limit, $date, $user_name)
    {
        $request = self::execute("select p.Title as title,post.Timestamp as Timestamp from post join user on post.AuthorId = user.UserId  join post p on post.ParentId = p.PostId where post.ParentId is not null and user.UserName = :name and post.Timestamp >= :date " . $limit, array("date" => $date, "name" => $user_name));
        return User::encode_activity_details($request, "Answer create/update");
    }

    private static function get_comment_activity($limit, $date, $user_name)
    {
        $request = self::execute("(select pp.title as title,c.timestamp as Timestamp from post p join comment c on p.PostId = c.PostId
        join post pp on p.ParentId = pp.PostId join user on c.UserId = user.UserId where p.ParentId is not null and user.UserName = :name and c.Timestamp >= :date)
        UNION
        (select p.title as title,comment.Timestamp as Timestamp from comment join user on comment.UserId = user.UserId
        join post p on p.PostId = comment.PostId where p.ParentId is null and user.UserName = :name and comment.Timestamp >= :date) " . $limit, array("date" => $date, "name" => $user_name));
        return User::encode_activity_details($request, "Comment update/create");
    }

    private static function encode_activity_details($request, $msg)
    {
        $request = $request->fetchAll();
        $str = "";
        foreach ($request as $row) {
            $title = json_encode($row["title"]);
            $time = json_encode($row["Timestamp"]);
            $type = json_encode($msg);
            $str .= "{\"content\":$title,\"time\":$time,\"type\":$type},";
        }
        return $str;
    }

    public static function is_mail_available(){
        $request = self::execute("select * from User where Email = :mail", array("mail" => $_POST["signupEmail"]));
            return $request->rowCount() == 0 ? "true" : "false";
    }
    
}
