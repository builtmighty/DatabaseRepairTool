<?php 
namespace builtmighty\tools\database;

class DatabaseTool {
    public static $error;
    private static $instance = null;
    private static 
    public static function getInstance(): DatabaseTool
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    /**
     * Make a new run. Returns Self
     */
    public static function make($Name, string $GetDataFunction, string $CallbackFunction ) : DatabaseRun {
        if(!is_callable(array('builtmighty\tools\database\Run', $GetDataFunction))){
            self::$error = 'Get Data Method doesn\'t exist';
            return false;
        }
        if(!is_callable(array('builtmighty\tools\database\Run', $CallbackFunction))){
            self::$error = 'Callback Method doesn\'t exist';
            return false;
        }

        $UI = new UserInterface::( $Name );

        $Data = call_user_func('builtmighty\tools\database\Run', $GetDataFunction);
        $Callable = array('builtmighty\tools\database\Run', $CallbackFunction);

        $Data = \apply_filters( 'dbtool_get_data', call_user_func('builtmighty\tools\database\Run', $GetDataFunction));


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