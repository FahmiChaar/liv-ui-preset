<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Biznas API",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class ApiBaseController
 */
class ApiBaseController extends Controller
{
    public function sendResponse($result, $message)
    {
        return Response::json($this->makeResponse($message, $result));
    }

    public function sendError($error, $code = 404)
    {
        return Response::json($this->makeError($error), $code);
    }

    /**
     * @param string $message
     * @param mixed  $data
     *
     * @return array
     */
    private function makeResponse($message, $data)
    {
        return [
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ];
    }

    /**
     * @param string $message
     * @param array  $data
     *
     * @return array
     */
    private function makeError($message, array $data = [])
    {
        $res = [
            'success' => false,
            'errors' => ['customError' => $message],
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return $res;
    }
}
