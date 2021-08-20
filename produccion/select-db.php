<?php


if ( isset($_GET["select_db"]) && ( $_GET["select_db"] == "true" ) ){
    
    $intDB = isset($_POST["db"]) ? intval($_POST["db"]) : 0;
    
    if ( intval($intDB) ){
        
        $_SESSION["db_connect_general"] = $intDB;
        
        print "<pre>";
        print_r($_SESSION["db_connect_general"]) . "                   db";
        print "</pre>";
        
    }
    
    
    
    die();
}


?>