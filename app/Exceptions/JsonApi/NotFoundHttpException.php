<?php

namespace App\Exceptions\JsonApi;

use Exception;

class NotFoundHttpException extends Exception
{
    public function render($request)
    {
        $id = $request->input('data.id');
        $type = $request->input('data.type');

        return response()->json([
            'errors' => [
                [
                    'title' => 'Not found',
                    'detail' => "The resource with id \"{$id}\" and type \"{$type}\" could not be found.",
                    'status' => '404',
                ]
            ]
        ], 404);
    }
}
