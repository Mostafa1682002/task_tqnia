<?php


function apiResponse($message, $data)
{
    $arr = [
        'status' => true,
        'message' => $message,
        'data' => $data
    ];
    return response()->json($arr, 200);
}



function successApi($message)
{
    $arr = [
        'status' => true,
        'message' => $message,
    ];
    return response()->json($arr, 200);
}

function errorApi($message, $status, $error = null)
{
    $arr = [
        'status' => false,
        'message' => $message,
        'errors' => $error
    ];
    return response()->json($arr, $status);
}
