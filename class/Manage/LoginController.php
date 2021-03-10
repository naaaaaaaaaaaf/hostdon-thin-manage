<?php


namespace Hostdon\Manage;


use Hostdon\Abstracts;
use Hostdon\CsrfValidator;
use Hostdon\DatabaseLoader;
use Hostdon\MessageConstructs;
use Valitron\Validator;

class LoginController extends Abstracts
{
    public function get_login(){
        session_start();
        $temp = self::LoadTwig('manage/login.html');
        return $temp->render(['token' => CsrfValidator::generate()]);
    }

    public function post_login(){
        session_start();
        $check = new SessionController();
        if (!CsrfValidator::validate($_POST['token'])) {
            exit();
        }
        $messages = new MessageConstructs();
        $v = new Validator($_POST);
        $v->rule('required', ['email', 'password'])->message('{field}は必須です。');
        //$v->rule('optional', ['digit']);
        $v->rule('email', 'email')->message('正しい{field}の書式で入力してください。');
        $v->rule('regex', 'password', '/[a-zA-Z0-9!@#$%^&*]{8,128}/')->message('正しい{field}の書式で入力してください。');
        $v->labels(array(
            'email' => 'メール',
            'password' => 'パスワード'
            //'recaptcha_response' => 'reCAPTCHA'
            //'digit' => '認証コード'
        ));
        if (!$v->validate()) {
            $data = $messages->reconstruct_message($v->errors(),'warning');
            $temp = self::LoadTwig('manage/login.html');
            return $temp->render(['messages' => $data,'token' => CsrfValidator::generate()]);
        }
        $db = new DatabaseLoader();
        $result = $db->db()->table('member')->select('*')->where('mail', $_POST['email'])->get()->toArray();
        if($result && password_verify($_POST['password'], $result[0]->password)){
            echo 'hoge';
            //$check->redirect_logined_session($_POST['email'],$result[0]->cusid);
        }else{
            $messages[] = 'gue-';
            $temp = self::LoadTwig('manage/login.html');
            return $temp->render(['messages' => $messages,'token' => CsrfValidator::generate()]);
        }
    }
    public function post_logout(){

    }

}