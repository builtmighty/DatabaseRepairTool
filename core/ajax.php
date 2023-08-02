<?php
namespace builtmighty\tools\database;

class Ajax {
    public static function init(){
        add_action( 'wp_ajax_bmdt_main', array(get_class(), 'bmdt_main'));
        add_action( 'wp_ajax_bmdt_continue', array(get_class(),'bmdt_continue'));
        add_action( 'wp_ajax_bdmt_end', array(get_class(),'bdmt_end'));
    }

    public static function bmdt_main(){
        #Setup
        $DBTool = DatabaseTool::getInstance();
        if( $DBTool::hasRun() ){
            #Start the run
   
            $Data_Array = $DBTool::getRun()->getData();

            #Get it into a Run CSV
            $filename = 'bmdb_' . date("Y-m-d-H:i:s");
            \update_option('bmdb_current_run', $filename);

            $filepath = \BuiltMightyDatabaseTool::get_plugin_path() . 'files/' . $filename . '.csv';
            if(file_exists($filepath))  unlink($filepath);
            
            $DBTool::getRun()::overwriteCSV($filepath, $Data_Array);
        
            if(!$Data_Array || empty($Data_Array)) {
                unlink($filepath);
                \delete_option('bmdb_current_run');
                echo json_encode(array('status'=>200, 'message'=>'No records with issues.'));
                die();
            } else {
                \do_action('dbtool_before');
                echo json_encode(array('status'=>100, 'message'=>'Run started...', 'count'=>count($Data_Array)));
                die();
            }
        }
    }
    public static function bmdt_continue(){
        $DBTool = DatabaseTool::getInstance();
        if( $DBTool::hasRun() ){
            
            $Record = $DBTool::getRun()->getRecord();

            if(!$Record){
                echo json_encode(array('status'=>500, 'message'=>'Record not found'));
                die(); 
            }
            if($Record === 200){
                self::bmdt_end();
            }

            $DBTool::getRun()->callbackRecord($Record);

            echo json_encode(array('status'=>100, 'message'=> 'Processed Record' ));
            die();

        } else {
            echo json_encode(array('status'=>404, 'message'=>'No Run Found'));
            die();  
        }

    }
    public static function bmdt_end(){
        #do end shit here
        $filename = \get_option('bmdb_current_run');
        $filepath = \BuiltMightyDatabaseTool::get_plugin_path() . 'files/' . $filename . '.csv';
        unlink($filepath);
        \delete_option('bmdb_current_run');
        \do_action('dbtool_after');
        echo json_encode(array('status'=>200, 'message'=> 'Finished and Cleaned Up.'));
        die();
    }


}
Ajax::init();