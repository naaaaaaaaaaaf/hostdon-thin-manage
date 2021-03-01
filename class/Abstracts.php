<?php


namespace Hostdon;


class Abstracts
{

    protected function twig(){
       $loader = new \Twig\Loader\FilesystemLoader('templates');
        return new \Twig\Environment($loader, [
            'cache' => 'cache',
            'auto_reload' => true
        ]);
   }
}