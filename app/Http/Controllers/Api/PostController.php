<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\ListPostsRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Platform;
use App\Models\Post;
use App\Services\PlatformValidation\PlatformValidatorFactory;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends BaseApiController
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(ListPostsRequest $request): JsonResponse
    {
       try {
        $response = $this->postService->list($request->validated(),Auth::id());
            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('Success',$response,self::STATUS_OKAY,false,true);
    }

    public function store(CreatePostRequest $request): JsonResponse
    {
       try {
            $data = $request->validated();
            $user = Auth::user();

            foreach ($data['platform_ids'] as $platformId) {
                $platform = Platform::findOrFail($platformId);
                $validator = PlatformValidatorFactory::make($platform->type);
                $validator->validate($data); 
            }
            $response = $this->postService->create($data,$user,$request->file('image'));
            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('Created Successfully',$response);
    }

    public function update(Post $post, UpdatePostRequest $request): JsonResponse
    {
       try {
           $data = $request->validated();
           $user = Auth::user();

        if (isset($data['platform_ids'])) {
            foreach ($data['platform_ids'] as $platformId) {
                $platform = Platform::findOrFail($platformId);
                $validator = PlatformValidatorFactory::make($platform->type);
                $validator->validate($data);
            }
        }

        $response = $this->postService->update($post, $data, $user->id);            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('Updated Successfully',$response);
    }

    public function destroy(Post $post): JsonResponse
    {
       try {
        $response = $this->postService->delete($post,Auth::id());
            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('deleted Successfully',$response);
    }

    public function analytics(): JsonResponse
    {
       try {
        $response = $this->postService->analytics(Auth::user());
            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('Success',$response);
    }

    
}
