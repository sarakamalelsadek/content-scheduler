<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class BaseApiController extends Controller
{
    use ApiResponseTrait;
    const LOG_TYPE_INFO = 'info';
    const LOG_TYPE_ERROR = 'error';
    const LOG_TYPE_WARNING = 'warning';

    public function responseSuccess(string $message = 'messages.success', $data = [], int $statusCode = self::STATUS_OKAY, bool $log= true,bool $paginate = false) :JsonResponse
    {
        return self::_sendResponse(true, $statusCode, $message, $data,$log,$paginate);
    }

    public function responseError(string $message = 'messages.error', $errors = [], int $statusCode = self::STATUS_BAD_REQUEST) :JsonResponse
    {
        return self::_sendResponse(false, $statusCode, $message, $errors);
    }

    public function hasPermission(string|array $permission, $user = null) :bool
    {
        // check if user
        if(!$user) $user = Auth::user();

        $check = false;
        if($user){
            // check array
            if(is_array($permission)){
                $check = $user->hasAnyPermission($permission);
            }
            // check string
            else{
                $check = $user->hasPermissionTo($permission);
            }
        }
        return $check;
    }

    protected function processPaginatedData($paginatedData, callable $callback)
    {
        if ($paginatedData instanceof LengthAwarePaginator) {
            $items = $paginatedData->getCollection()->map($callback);
            $paginatedData->setCollection($items);
        }

        return $paginatedData;
    }

}
