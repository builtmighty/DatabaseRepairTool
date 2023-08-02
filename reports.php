<?php
namespace builtmighty\tools\database;

Class Run extends DatabaseRun {

    public static function init(){

        DatabaseTool::make('Test Function', 'GetDataFunction', 'CallbackFunction')
        ->addKeys(['ID', 'Title'])
        ->addDataFilter('Filter_function')
        ->addToInit('Fires_Before_Processing')
        ->addToAfter('Fires_After_Processing')
        ->register();
    }
    /**
     * Accepts no arguments. 
     * Returns array
     */
    public static function GetDataFunction() : array {
        error_log("Attempting to do data function");
        global $wpdb;
        $posts = $wpdb->prefix . 'posts';
        $postmeta = $wpdb->prefix . 'postmeta';
        
        #Select Something

        $records = $wpdb->get_results(
            "SELECT {$posts}.ID, {$posts}.post_title
            FROM $posts LIMIT 10", ARRAY_A
        );
        return $records;
    }
    /**
     * Accepts/Maps the data rows of the Get Data Function
     * Returns nothing 
     */

    public static function CallbackFunction( array $row ) : void {

        $post = get_post( $row['ID']);
        $title = $row['Title'];
        error_log("TITLE: " . $title . ':' . $post->ID);
        #do something
    
    }
    /**
     * Hook into the data after it has been queried but before it has been processed
     */
    public static function FilterFunction( $DATA ){
        #do something with the data;
        error_log("Filter Fired");
        return $DATA;
    }
    /**
     * Action after the data has been set at the beginning of the run
     */
    public static function Fires_Before_Processing(){
        #This action fires after the data/run has been set up but before it's been processed
        error_log("Init Action Fired");
    }
    /**
     * Action after the run has been completed
     */
    public static function Fires_After_Processing(){
        #This action fires after the data/run has been set up but before it's been processed
        error_log("After Action Fired");
    }
}
Run::init();