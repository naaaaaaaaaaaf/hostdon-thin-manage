<?php


namespace Hostdon\Manage;


use Hostdon\Abstracts;

class ManageController extends Abstracts
{
    public function index(){
        $check = new SessionController();
        $check->require_logined_session();

        return 'aaa';
    }
}