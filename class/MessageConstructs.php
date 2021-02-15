<?php


namespace Hostdon;

// 配列にMessageを格納していくクラス
class MessageConstructs
{
    private array $messages;
    function __construct(){
        $this->messages = array();
    }
    public function add_message(string $msg, string $level): string
    {
    $data = "<div class=\"alert alert-${level}\" role=\"alert\">${msg}</div>";
    return $this->messages[] = $data;
    }
}