<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    // Response Status Code
    const STATUS_OKAY = 200;
    const STATUS_CREATED = 201;
    const STATUS_ACCEPTED = 202;
    const STATUS_NON_AUTHORIZE = 203;
    const STATUS_NO_CONTENT = 204;
    const STATUS_RESET_CONTENT = 205;
    const STATUS_FOUND = 302;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_PAYMENT_REQUIRED = 402;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;
    const STATUS_METHOD_NOT_ALLOWED = 405;
    const STATUS_NOT_ACCEPTABLE = 406;
    const STATUS_CONFLICT = 409;
    const STATUS_LENGTH_REQUIRED = 411;
    const STATUS_PRECONDITION_FAILED = 412;
    const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
    const STATUS_UNPROCESSABLE_ENTITY = 422;
    const STATUS_SERVER_ERROR = 500;
    const STATUS_NOT_IMPLEMENTED = 501;

    public const SUCCESS = true;
    public const FAILED = false;

    public function _sendResponse(bool $success = true, int $status_code = self::STATUS_OKAY, string $message = '', $data = [], bool $logResponse = true, bool $paginatedData = false, $forceObject = false):JsonResponse
    {
        if ($success) {
            if ($paginatedData) {
                $response = [
                    'message' => $message,
                    'status' => $status_code
                    ];

                // check if instance of paginator or not
                if($data instanceof LengthAwarePaginator){
                    $response['meta'] = [
                        'total' => $data->total(),
                        'per_page' => $data->perPage(),
                        'current_page' => $data->currentPage(),
                        'last_page' => $data->lastPage(),
                        'from' => $data->firstItem(),
                        'to' => $data->lastItem(),
                    ];
                    $response['data'] = $data->items();
                }
                // check if data is array and paginated
                elseif(isset($data['pagination'])){
                    $response['meta'] = [
                        'total' => $data['pagination']['total'],
                        'per_page' => $data['pagination']['per_page'],
                        'current_page' => $data['pagination']['current_page'],
                        'last_page' => $data['pagination']['last_page'],
                        'from' => $data['pagination']['from'],
                        'to' => $data['pagination']['to'],
                    ];
                    $response['data'] = $data['data']??$data;
                }
                // check if array only
                else{
                    $response['meta'] = [
                        'total' => count($data),
                        'per_page' => count($data),
                        'current_page' => 1,
                        'last_page' => 1,
                        'from' => 0,
                        'to' => count($data),
                    ];
                    $response['data'] = $data;
                }
            } else {
                $response = [
                    'message' => $message,
                    'status' => $status_code,
                    'data' => $data
                ];
            }
        } else {
            $response = [
                'message' => $message,
                'status' => $status_code,
                'errors' => $data
            ];
        }
        if ($forceObject) {
            return response()->json($response, $status_code, options: JSON_FORCE_OBJECT);
        }
        return response()->json($response, $status_code);
    }
}
