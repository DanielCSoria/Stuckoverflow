<?php

require_once "framework/Model.php";
require_once "model/Post.php";
require_once "model/User.php";
require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "model/Vote.php";

class Answer extends Post
{
    private $parrent_id;
    public function __construct($author, $body, $parrent_id = NULL, $post_id = NULL, $timestamp = NULL)
    {
        parent::__construct($author, $body, $post_id, $timestamp);
        $this->parrent_id = $parrent_id;
    }
    
    public function get_parrent_id()
    {
        return $this->parrent_id;
    }

    public function validate()
    {
        $errors = parent::validate();
        if ($this->parrent_id == NULL) {
            $errors[] = "Answers must have ParentId";
        }
        return $errors;
    }

    public function is_a_question()
    {
        return false;
    }

    public function update_db()
    {
        parent::execute("UPDATE POST SET Body=:body,Timestamp = CURRENT_TIMESTAMP where PostId = :id", array("body" => $this->get_body(), "id" => $this->get_id()));
    }

    public function insert_db()
    {
        parent::execute("INSERT INTO post (AuthorId, Body,ParentId) VALUES (:author,:body,:id)", array("author" => $this->get_author()->get_id(), "body" => $this->get_body(), "id" => $this->parrent_id));
    }

    public function can_be_accepted($user)
    {
        $parrent = Post::get($this->parrent_id);
        return $user->is_admin() || ($this->can_be_unaccepted($user) && $parrent->get_accepted() != $this->get_id());
    }

    public function can_be_unaccepted($user)
    {
        $parrent = Post::get($this->parrent_id);
        return $user->is_admin() || $parrent->get_author() == $user;
    }

    public function must_show_accept_ico($user)
    {
        return $user  && !$this->is_accepted()  && $this->can_be_accepted($user);
    }

    public function must_show_unaccept_ico($user)
    {
        return $user  && $this->is_accepted() &&  $this->can_be_unaccepted($user);
    }

    public function confirm_answer()
    {
        self::execute('update post set AcceptedAnswerId = :id where PostId = :pid', array("id" => $this->get_id(), "pid" => $this->parrent_id));
        return $this;
    }

    public function unconfirm_answer()
    {
        self::execute('update post set AcceptedAnswerId = null where PostId = :pid', array("pid" => $this->parrent_id));
        return $this;
    }

    public function delete()
    {
        // delete AcceptedAnswerId du parent si la réponse est acceptée
        if ($this->is_accepted()) {
            parent::execute("update post set AcceptedAnswerId = null where PostId = :id", array("id" => $this->parrent_id));
        }
        return  parent::delete();
    }

    public function is_accepted()
    {
        $query = self::execute('select * from post where AcceptedAnswerId = :id', array("id" => $this->get_id()));
        if ($query->rowCount() === 0) {
            return false;
        }
        return true;
    }
}
