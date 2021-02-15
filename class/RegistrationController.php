<?php


namespace Hostdon;


use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RegistrationController extends Abstracts
{

    public function index(): string
    {
        $_SESSION['token'] = CsrfValidator::generate();
        $temp = self::twig()->load('registration/email.html');
        $messages[] = (new MessageConstructs)->add_message('メールアドレスを入力してください','info');

        return $temp->render(['token' => $_SESSION['token'], 'messages' => $messages]);
    }
    public function email_post(): string
    {
        //if post
        $test = (new DatabaseLoader)->db()->table('test_member')->select('*')->where('mail', $_POST['email'])->get()->toArray();;

        $temp = self::twig()->load('email.html');
        return $temp->render();
    }
}