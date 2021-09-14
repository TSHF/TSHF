<?php

/**
 * Description of constant
 *
 * @author mohit
 */
return [
    'errorCodes' => [
        500 => 'There is a technical issue',
        521 => 'Invalid Token',
        522 => 'Invalid Secret Key',
        600 => 'There is some Sql query exception',   
        700 => 'Invalid request parameter', /** Required request data is not given ***/   
        800 => 'Data type mismatch ',/**** Data Type mismatch **/
        404 => 'Data Not Found',
        750 => 'Duplicate value given'
        
    ],
    'STATUS_SUCCESS'=>'success',
    'STATUS_CODE'=>200,
    "IMAGE_URL" => env('APP_ENV') == 'local' ? 'http://localhost/Levo/b2c/public/images/' : "http://".$_SERVER['HTTP_HOST']."/images/",
    "IMAGE_ROOT_PATH" => $_SERVER['DOCUMENT_ROOT'].'/Levo/api/public/images/'
    

];