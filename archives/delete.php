<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Methods,Content-Type,Authorization, X-Requested-With');

include_once '../config/config.php';
include_once '../config/Database.php';
include_once '../models/Archive.php';
include_once '../models/Pinned.php';
include_once '../utilities/Response.php';

//CHECK IF ID IS SET
$id = isset($_GET['id']) ? $_GET['id'] : false;
if(!$id){
    //create response
    $res = new Response(1);
    $res->message = "No id was specified";
}
else{
    //INITIATE A NEW MODEL
    $archive1 = new Archive();
    $res = new Response();

    //EXECUTE deletion
    $archive1->id = $id;
    if($archive1->deleteOne()){
        //delete from pinned items as well
        $res->message= "Document with Id ($id) successfully Deleted";


    }
    else{
        $res = new Response(1);
        $res->message = "Failed to Delete document";
        //CHECK IF THE ERROR WAS A 404
        $res->error = $archive1->error;
    }
}