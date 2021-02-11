<?php


namespace Hostdon;


use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseLoader
{
    /**
     * @var Capsule
     */
    private Capsule $capsule;

    function __construct()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => DB_HOST,
            'database'  => DB_NAME,
            'username'  => USER,
            'password'  => PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $this->capsule = $capsule;
    }

    public function db(): Capsule
    {
        return $this->capsule;
    }
}