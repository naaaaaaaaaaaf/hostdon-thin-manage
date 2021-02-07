<?php


namespace Hostdon;


class Abstracts
{
   protected function twig(){
       $loader = new \Twig\Loader\FilesystemLoader('templates');
       $twig = new \Twig\Environment($loader, [
           'cache' => 'cache',
       ]);
       return $twig;
   }
}