<?php

namespace App\Traits;

trait Response{
    protected function success($data=null, $message=null, $code=200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error($data=null, $message=null, $code)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
