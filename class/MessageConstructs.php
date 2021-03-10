<?php


namespace Hostdon;

// 配列にMessageを格納していくクラス
class MessageConstructs
{
    private array $messages;

    function __construct()
    {
        $this->messages = array();
    }

    public function add_message(string $text, string $level): string
    {
        $data = "<div class=\"alert alert-${level}\" role=\"alert\">${text}</div>";
        return $this->messages[] = $data;
    }
    public function reconstruct_message(array $messages_array,string $level): array
    {
        $data = array();
        foreach ($messages_array as $values){
            foreach ($values as $value ){
                $data[] = $this->add_message($value,$level);
            }
        }
        return $data;
    }
}