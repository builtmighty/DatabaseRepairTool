<?php
/*
 * Plugin Name:       Seal & Cylinder Database Repair Tool
 * Plugin URI:        https://sealandcylinder.com/
 * Description:       Find and Update duplicate SKUs in 
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      8.0
 * Author:            Built Mighty
 * Author URI:        https://builtmighty.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sealcylinder-dbrepair
 * Domain Path:       /languages
 */


 defined( 'ABSPATH' ) OR exit;

 if ( ! class_exists( 'BuiltMightyDatabaseTool' ) ) {
 
     register_activation_hook( __FILE__, array ( 'BuiltMightyDatabaseTool', 'register_activation_hook' ) );    
     add_action( 'plugins_loaded', array ( 'BuiltMightyDatabaseTool', 'get_instance' ), 5 );
     
     class BuiltMightyDatabaseTool {
  
         private static $instance = null;
 
         // Plugin Settings
        const version = '1.0.0';
        const text_domain = 'bm_database_tool'; 
         public static function get_instance() 
         {
 
             if( null == self::$instance ) {
 
                 self::$instance = new self;
             }
             return self::$instance;
 
         }
         
         private function __construct() {
 
             // actvation ##
             \register_activation_hook( __FILE__, array( get_class(), 'register_activation_hook' ) );
 
             // deactvation ##
             \register_deactivation_hook( __FILE__, array( get_class(), 'register_deactivation_hook' ) );
 
             // load libraries ##
             require_once self::get_plugin_path() . 'core/core.php';
             require_once self::get_plugin_path() . 'reports.php';
 
         }
         

 
         /* UTILITY FUNCTIONS */
 
         public static function register_activation_hook() {
 
             $option = self::text_domain . '-version';
             \update_option( $option, self::version ); 
                 
        }
        public static function register_deaactivation_hook() {
 
            $option = self::text_domain . '-version';
            \delete_option( $option, self::version ); 
            
                
       }
 

 
         public static function get_plugin_url( $path = '' ) 
         {
 
             return plugins_url( $path, __FILE__ );
 
         }
         
         public static function get_plugin_path( $path = '' ) 
         {
 
             return plugin_dir_path( __FILE__ ).$path;
 
         }
 
     }
 
 }




// add_action( 'admin_menu', 'snc_data_repair_page' );
// add_action( 'wp_ajax_sealcylinderrepairdb', 'sealcylinderrepairdb');
// add_action( 'wp_ajax_sealcylinderrepairdb_continue', 'sealcylinderrepairdb_continue');
// add_action( 'wp_ajax_sealcylinderrepairdb_end', 'sealcylinderrepairdb_end');



// /**
//  * Returns Array fo the Source Repair File in the Plugin
//  */
// function getCSV(){
//     return array_map('str_getcsv', file( __DIR__ . '/files/VariationsList.csv'));
// }

// function getRunRecord(){
//     if(!file_exists( __DIR__ . '/files/CurrentRun.csv')) return false;
//     if ( 0 == filesize(__DIR__ . '/files/CurrentRun.csv') ) return 200;
//     $toArray = array_map('str_getcsv', file( __DIR__ . '/files/CurrentRun.csv'));

//     #if only the headers remain
//     if(count($toArray) === 1 )return 200;

//     $record = array_pop($toArray);
//     newDataRun( $toArray );
//     return $record;
// }


// function CSVtoTable(){
//     $rows = getCSV();
//     $header = array_shift($rows);
//     #Let's dump to a table
//     $html = '<table id="db_repair_values" style="table-layout:fixed; margin: 0 auto; max-width:80%;">
//             <thead style="display:table-header-group;"><tr>';
//     foreach($header as $head){
//         $html.='<th>'. $head . '</th>';
//     }
//     $html .='</tr></thead><tbody">';
//     #now file is built into array variable
//     foreach($rows as $row){ 
//         $html.='<tr>';
//         foreach($row as $col) $html.='<td>'.$col.'</td>';
//         $html.='</tr>';   	 									
//     }
//     $html.='</tbody><table>';

//     return $html;
// }


// function sealcylinderrepairdb(){
//     #sets up new Data Run
//     $text = get_problem_posts();
//     if($text  == 'No records with issues. Cheers.') {
//         echo json_encode(array('status'=>200, 'message'=>$text));
//         die();
//     } else {

//         echo json_encode(array('status'=>100, 'message'=>$text[0], 'count'=>$text[1]));
//         die();
//     }
// }
// function sealcylinderrepairdb_end(){
//     #do end shit here
//     echo json_encode(array('status'=>200, 'message'=> 'Finished.'));
//     die();
// }

// function sealcylinderrepairdb_continue(){
//     $SETTINGS_BLANKSONLY = true;
//     $Prefix = 'Skipping ';
//     $record = getRunRecord();
//     if(!$record){
//         echo json_encode(array('status'=>400, 'message'=>'Could not open record. Aborted'));
//     }
//     if($record === 200 ){
//         echo json_encode(array('status'=>200, 'message'=> 'Finished! Cleaning up.'));
//     }
    
//     #quick hack for now because we're not reusing this code
//     $record = array_combine(array('VID','SKU','Seal Type','Material','Temp','Pressure','Description','ID','OD','L','HT','CS'), $record);
//     error_log("RECORD x" . print_r($record,true));
//     $variation = wc_get_product($record['VID']); 
//     if( $variation && $variation->variation_is_visible() ){ //Don't replace variations that are fully unset

//         $post_parent = $variation->get_parent_id();
//         $record_attributes = [];
//         $record_attributes['pa_height'] = isset($record['HT']) ? str_replace('.', '-', $record['HT']) : null;
//         $record_attributes['pa_cross-section'] = isset($record['CS'])?str_replace('.', '-',$record['CS'])  : null;
//         $record_attributes['pa_od'] = isset($record['OD']) ? str_replace('.', '-',$record['OD'])  : null;
//         $record_attributes['pa_id'] = isset($record['ID']) ? str_replace('.', '-', $record['ID'])  : null;

//         $db_attributes = $variation->get_attributes();
//         $new_attributes = [];
//         error_log("Attributes" . print_r($db_attributes, true));
//         foreach($db_attributes as $nme=>$opt){
            
//             #check if options are blank
//             if(empty($opt)){
//                 if(! is_null($record_attributes[$nme])){ #we want to keep zeros, we don't want to keep null
//                     $Prefix = 'Processing ';
//                     $new_attributes[$nme] = $record_attributes[$nme];
//                 }
     
                
//             } 
//         }
//         if(!empty($new_attributes)) $db_attributes = array_merge($db_attributes, $new_attributes);
//         error_log("New Attributes "  . print_r($db_attributes, true));
//         $variation->set_attributes($db_attributes);
//         $variation->save();
//         $variation->save_meta_data();        
//         WC_Product_Variable::sync( $post_parent );
//         $product = wc_get_product( $post_parent );
//         $product->save(); // Save the data 
//         //}  
//     }
    
//     echo json_encode(array('status'=>100, 'message'=> $Prefix . 'record ' . $record['VID']));
//     die();
// }
// function get_problem_posts(){
//     global $wpdb;
//     $records = $wpdb->get_col(
//         "SELECT wp_posts.ID 
//         FROM wp_posts INNER JOIN wp_postmeta ON wp_posts.ID = wp_postmeta.post_id
//         WHERE wp_posts.post_type = 'product_variation'
//         AND wp_postmeta.meta_key = '_first_variation_attributes'"
//     );
//     if($records){
//         $currentRun = [];

//         $init_text = 'Found ' . count($records) . ' records set up incorrectly in the database.';
//         $data_file = getCSV();
//         $headers = array_shift($data_file);
//         // $a_array = array_combine($headers, $data_file);
//         $a_array = array_map(function($x) use ($headers){
//             return array_combine($headers, $x);
//         }, $data_file);
//         $currentRun[0] = $headers;
//         foreach($a_array as $record){
//             if(in_array( $record['VID'], $records)){
//                 $currentRun[] = $record;
//             }
//         }
//         $init_text .= ' Matched ' . count( $currentRun ) . ' records from the data sheet.';
//         #Set the current run

//         newDataRun( $currentRun );
//     } else {
//         unlink( __DIR__ . '/files/CurrentRun.csv');
//         return 'No records with issues. Cheers.';
//     }
//     $count = count( $currentRun );
//     return array($init_text, $count);
// }
// function newDataRun( $arr ){

//     if( !$arr || empty($arr) ) return false;
    
//     $fp = fopen(__DIR__ . '/files/CurrentRun.csv', 'w+');
//     $headers = array_shift($arr);

//     fputcsv($fp, $headers);
    
//     foreach ($arr as $row) {
//         fputcsv($fp, $row);
//     }
//     rewind($fp); 
//     fclose($fp);
// }