<?php


namespace Hostdon\Manage;


class SessionController
{
    public function require_unlogined_session () {
        // セッション開始
        @session_start();
        // ログインしていれば
        if (isset($_SESSION["mail"])) {
            header('Location: /');
        }
    }
    public function require_logined_session() {
        // セッション開始
        @session_start();
        // ログインしていなければlogin.phpに遷移
        if (!isset($_SESSION["mail"])) {
            header('Location: /login');
        }
    }
    public function redirect_logined_session($mail,$cusid){
        @session_start();
        session_regenerate_id(true);
        //ユーザ名をセット
        $_SESSION['mail'] = $mail;
        $_SESSION['cusid'] = $cusid;
        // ログイン後に/に遷移
        header('Location: /');
    }
}