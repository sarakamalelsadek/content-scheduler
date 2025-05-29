<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Models\Platform;
use App\Services\PlatformService;
use Illuminate\Support\Facades\Auth;

class PlatformController extends BaseApiController
{
    protected $platformService;

    public function __construct(PlatformService $platformService)
    {
        $this->platformService = $platformService;
    }

    public function userPlatForms()
    {
        try {
        $response = $this->platformService->userAvaliblePlatforms(Auth::user());
            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('Success',$response);
    }

    public function toggleUserPlatform(Platform $platform)
    {
        try {
        $response = $this->platformService->toggleUserPlatform($platform->id ,Auth::user());
            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('Success',$response);
    }


}
