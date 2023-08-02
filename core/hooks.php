<?php
namespace builtmighty\tools\database;

class Hooks {
    public static function hooks_init(){
        \add_filter( 'dbtool_get_data', array(get_class(), 'get_data_callback' ) ); 
        \add_action( 'dbtool_init', array(get_class(), 'before'));
        \add_action( 'dbtool_after', array(get_class(), 'after'));
    }

    public static function get_data_callback( $thedata ){ return $thedata; }
    public static function before(){return true;}
    public static function after(){return true;}
}
Hooks::hooks_init();
