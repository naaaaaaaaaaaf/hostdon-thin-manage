<?php


namespace Hostdon;


class RegistrationController extends Abstracts
{

    public function index(): string
    {
        $_SESSION['token'] = CsrfValidator::generate();
        // $test = (new DatabaseLoader)->capsule()->table('test_member')->select('*')->where('mail', $_POST['email'])->get()->toArray();;
        $temp = self::twig()->load('registration/email.html');
        return $temp->render(['token' => $_SESSION['token']]);
    }
    public function email_post(): string
    {
        //if post
        $temp = self::twig()->load('email.html');
        return $temp->render();
    }
}