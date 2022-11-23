<?php

function getErrorUnsupportedFormat() {
    $error_data = array(
        "error:" => "unsuportedResponseFormat",
        "message:" => "The requested resouce representation is available only in JSON."
    );
    return $error_data;
}


function makeCustomJSONError($error_code, $error_message, $data = "") {
    $error_data = array(
        "error:" => $error_code,
        "message:" => $error_message
    );    
    if (!empty($data)) {
        $error_data["data"] = $data;
    }

    return json_encode($error_data);
}

/*
function makeCustomJSONSuccess($error_code, $error_message, $data) {
    $error_data = array(
        "error:" => $error_code,
        "message:" => $error_message,
        "data: " => $data
    );    
    return json_encode($error_data);
}
*/