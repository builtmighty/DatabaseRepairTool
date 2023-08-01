<?php 
namespace builtmighty\tools\database;

class DatabaseTool {
    private static $instance = null;
    public static function getInstance(): DatabaseTool
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct(){ 
        include_once __DIR__ . '/hooks.php';
        include_once __DIR__ . '/tables.php';
        include_once __DIR__ . '/user-interface.php';
        include_once __DIR__ . '/ajax.php';
        include_once __DIR__ . '/report.php';
    }


    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

}