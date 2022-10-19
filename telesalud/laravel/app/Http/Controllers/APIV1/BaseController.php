<?php
namespace App\Http\Controllers\APIV1;

use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{

    /**
     *
     * @var integer
     */
    private $limit = 50;

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result)
    {
        /*
         * if ($raw) {
         * $response = ;
         * } else {
         * $response = [
         * 'success' => true,
         * 'data' => $result,
         * 'message' => $message
         * ];
         * }
         */
        return response()->json($result, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];
        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
}
