<?php 
namespace builtmighty\tools\database;

class DatabaseRun extends DatabaseTool {
    private $filters = array();
    private $init = array();
    private $after = array();
    private $keys = array();
    public function __construct( $DataCallable, $Callable){
        $this->DataCallable = $DataCallable;
        $this->Callback = $Callable;
    }
    protected function register(){
        $DBTool = DatabaseTool::getInstance();
        if(!empty($this->filters)){
            $filters = array_unique($this->filters);
            foreach($filters as $callable){
                \add_filter( 'dbtool_get_data', $callable); 
            }
        }
        if(!empty($this->init)){
            $init = array_unique($this->init);
            foreach($init as $callable){
                \add_action( 'dbtool_init', $callable);
            }
        }
        if(!empty($this->after)){
            $after = array_unique($this->after);
            foreach($after as $callable){
                \add_action( 'dbtool_after', $callable);
            }
        }

        $DBTool::addRun($this);
    }
    public function addDataFilter( string|Array $maybeCallable ){
        if(is_callable($maybeCallable)){

            $this->filters[] = $maybeCallable;
            return $this;
        } else if(gettype($maybeCallable) === 'string'){
            if(is_callable(array('builtmighty\tools\database\Run', $maybeCallable ))){

                $this->filters[] = array('builtmighty\tools\database\Run', $maybeCallable );
                return $this;
            }
        } 
        return $this;
        
    }
    
    public function addToInit( string|Array $maybeCallable ){
        if(is_callable($maybeCallable)){

            $this->init[] = $maybeCallable;
            return $this;
            
        } else if(gettype($maybeCallable) === 'string'){
            if(is_callable(array('builtmighty\tools\database\Run', $maybeCallable ))){

                $this->init[] = array('builtmighty\tools\database\Run', $maybeCallable );
                return $this;
            }
        } 
        return $this;
        # \add_action('dbtool_init', $maybeCallable);
    }
    public function addToAfter( string|Array $maybeCallable ){
        if(is_callable($maybeCallable)){

            $this->after[] = $maybeCallable;
            return $this;
            
        } else if(gettype($maybeCallable) === 'string'){
            if(is_callable(array('builtmighty\tools\database\Run', $maybeCallable ))){

                $this->after[] = array('builtmighty\tools\database\Run', $maybeCallable );
                return $this;
            }
        } 
        return $this;
        # \add_action('dbtool_init', $maybeCallable);
    }
    public function addKeys( array $keys ){
        $this->keys = $keys;
        return $this;
    }
    public function useHeaderKeys(){
        #Not sure how to do this
        return $this;
    }

    public static function overwriteCSV($filepath, $array){
        // Open a file in write mode ('w')
        $run = fopen($filepath, 'w');
        
        // Loop through file pointer and a line
        foreach ($array as $fields) {
            fputcsv($run, $fields);
        }
        fclose($run);
    }

    public function getData(){
        return \apply_filters('dbtool_get_data', call_user_func($this->DataCallable) );
    }
    public function getRecord(){
        $filename = \get_option('bmdb_current_run');
        $filepath = \BuiltMightyDatabaseTool::get_plugin_path() . 'files/' . $filename . '.csv';
        if(!file_exists($filepath)) {
            return false;
        }
        if( 0 == filesize($filepath)){
            return 200;
        } 
        $FileToArray = array_map('str_getcsv', file( $filepath ));
        $next= array_pop($FileToArray);

        #Array mutates, overwrite the old CSV with the new smaller one
        self::overwriteCSV($filepath, $FileToArray);

        return $next ? $next : false;

    }


    public function callbackRecord($Record){
        if(!empty($this->keys)) $Record = array_combine($this->keys, $Record);
        return call_user_func($this->Callback, $Record);
    }
}


