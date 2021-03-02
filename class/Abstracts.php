<?php


namespace Hostdon;


use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Abstracts
{
    protected function LoadTwig($template): \Twig\TemplateWrapper
    {
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader, [
            'cache' => 'cache',
            'auto_reload' => true
        ]);
        try {
            return $twig->load($template);
        } catch (LoaderError $e) {
            die('LoaderError! <br>'.$e);
        } catch (RuntimeError $e) {
            die('RuntimeError! <br>'.$e);
        } catch (SyntaxError $e) {
            die('SyntaxError! <br>'.$e);
        }
    }
}