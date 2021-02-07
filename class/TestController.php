<?php


namespace Hostdon;


class TestController extends Abstracts
{
    public function index(){
        $param = ['name'];
        $temp = self::twig()->load('index.html');
        return $temp->render();
    }
}