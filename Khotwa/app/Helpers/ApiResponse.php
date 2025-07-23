<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = null, $message = 'Operation successful', $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error($message = 'Something went wrong', $status = 500, $data = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function validation($errors, $message = 'Validation failed', $status = 422)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $errors,
        ], $status);
    }
}
