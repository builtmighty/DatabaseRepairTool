<?php
namespace builtmighty\tools\database;

Class Run {

    public static function init(){

        DatabaseTool::make('Test Function', 'GetDataFunction', 'CallbackFunction');
    }
    /**
     * Accepts no arguments. 
     * Returns array
     */
    private static function GetDataFunction() : array {
        global $wbdb;
        $posts = $wpdb->prefix . 'posts';
        $postmeta = $wpdb->prefix . 'postmeta';
        
        #Select Something

        $records = $wpdb->get_results(
            "SELECT {$posts}.ID, {$posts}.post_title
            FROM $posts WHERE {$posts}.post_title LIKE '%Believe%'", ARRAY_A
        );
        return $records;
    }
    /**
     * Accepts/Maps the data rows of the Get Data Function
     * Returns nothing 
     */

    private static function CallbackFunction( array $row ) : null {

        $post = get_post( $row['ID']);
        $title = $row['title'];
        
        #do something
    
    }

}
Run::init();