<?php


namespace Hostdon;


class RegistrationController extends Abstracts
{
    function __construct(){
        @session_start();
    }
    public function index(): string
    {
        $_SESSION['token'] = CsrfValidator::generate();

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