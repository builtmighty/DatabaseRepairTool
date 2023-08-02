<?php 
namespace builtmighty\tools\database;

class UserInterface extends DatabaseTool {

    public function __construct( bool|string $Name ){
        \add_action( 'admin_menu', function() use ($Name){
            call_user_func( array('builtmighty\tools\database\UserInterface', 'data_repair_page'), $Name );
        });

    }

    function data_repair_page( $Name ) {
        if(!$Name){

        } else {
            \add_menu_page( 'BuiltMighty Database Repair', 'BuiltMighty Database Repair', 'manage_options', 'fix-data.php', function() use ($Name){
                call_user_func( array('builtmighty\tools\database\UserInterface', 'bmdb_page_function'), $Name );
            }, 'dashicons-admin-tools', 20 );
        }


        #\add_menu_page( 'BuiltMighty Database Repair', 'BuiltMighty Database Repair', 'manage_options', 'fix-data.php', 'snc_page_function', 'dashicons-admin-tools', 6  );
    }
    function bmdb_page_function_false(){
        ?>
        <div class="container">
            <?php echo self::buttonJS( $Name ) ?>
            <h2>Built Mighty Database Repair Tool</h2>

            <div class="record-container results-area">
                <h4>Your current setup has errors. Go to the reports.php file in the plugin and check your data and callback functions</h4>
                <?php # echo CSVtoTable() ?>
            </div>
    
        <?   
    }
    function bmdb_page_function( $Name ){
        ?>
        <div class="container">
            <?php echo self::buttonJS( $Name ) ?>
            <h2>Built Mighty Database Repair Tool</h2>
            <div class="head">
                <button id="repairDB" onclick="doRepairDatabase(event)"><?php echo $Name ?></button>
            </div>
            <div class="record-container results-area">
                <h4>Note: This could take a long time</h4>
                <?php # echo CSVtoTable() ?>
            </div>
    
        <?
    }
    function buttonJS( $Name ){
        $slug  = \sanitize_title( $Name );
        ?><script>
            async function fetchLoop(resp, container){
    
                const cont = await fetch( ajaxurl + '?action=bmdt_continue' );
                const prog = await cont.json();
                const processed = parseInt(container.getAttribute('data-processed-records'));
                const total = parseInt(container.getAttribute('data-total-records'));
                const processing = Math.min(total, (processed + 1));
                container.setAttribute('data-processed-records', processed + 1);
                const prefix = '<span>processing ' + processing + ' of ' + total + ' records: </span>';
                console.log(prog);
                if(container) container.innerHTML = '<div>' + prefix + prog.message + '</div>';
                stat = prog.status;
                if(prog.status === 100 && processed <= total ){
                    fetchLoop(prog, container);
                } else {
                return prog;
                }
            }
            async function doRepairDatabase(e){
                const container = e.target.closest('.container').querySelector(".results-area");
                if(container) container.innerHTML = 'Loading...';
                const response = await fetch( ajaxurl + '?action=bmdt_main' );
                const ret = await response.json();
                console.log(ret);
                container.setAttribute('data-total-records', ret.count);
                container.setAttribute('data-processed-records', "0");
                if(container) container.innerHTML = ret.message;
                if(ret.status === 100) {
                    let finished = await fetchLoop(ret, container);
                    let finishedjson = await finished.json();
                    if(finishedjson.status == 200){
                        const the_end = await fetch( ajaxurl + '?action=bmdt_end' );
                        const final = await the_end.json();
                        console.log("THE END "); console.log(the_end);
                        if(container) container.innerHTML = the_end.message;
                    }
                }
            }
    
        </script><?php
    }
}