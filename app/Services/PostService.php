<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostPlatform;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostService
{
    /**
     * Retrieve all posts with optional filters.
     */
    public function list(array $filter, $userId): LengthAwarePaginator
    {
        $query = Post::with([
            'platforms' => function($q){
                    $q->select('platforms.id','name','type');
                },
             ])
        ->where('user_id',$userId)
        ->where(function($query) use($filter){
                
                if(isset($filter['status'])){
                    $query->where('status',$filter['status']);
                }
                if(isset($filter['date_from'])) {
                    $dateFrom = Carbon::parse($filter['date_from'])->startOfDay()->toDateTimeString();
                    $query->where('scheduled_time', '>=',$dateFrom);
                }
                if(isset($filter['date_to'])) {
                    $dateTo = Carbon::parse($filter['date_to'])->endOfDay()->toDateTimeString();
                    $query->where('scheduled_time', '<=',$dateTo);
                }
                
            })
            ->orderBy('updated_at','DESC');


        return $query->paginate($filter['per_page']?? 10);
    }

    /**
     * Create a new post.
     */
    public function create(array $data,User $user, ?UploadedFile $image = null)
    {
        // Count user's scheduled posts for the same day
        $scheduledCount = Post::where('user_id', $user->id)
            ->whereDate('scheduled_time', Carbon::parse($data['scheduled_time'])->toDateString())
            ->where('status', Post::STATUS_SCHEDULED)
            ->count();

        if ($scheduledCount >= 10) {
            throw new \Exception('You have reached the limit of 10 scheduled posts for this day.');
        }
        return DB::transaction(function () use ($data,$user,$image) {
            $imagePath = null;
            if ($image) {
                $imagePath = $image->store('posts', 'public');
            }

            // check active platforms
            $activePlatformIds = $user->activePlatforms()
                ->pluck('platforms.id')
                ->toArray();

            $selectedPlatformIds = $data['platform_ids'];
            $invalidPlatforms = array_diff($selectedPlatformIds, $activePlatformIds);

            if (count($invalidPlatforms)) {
                throw new \Exception('One or more selected platforms are not active for this user.');
            }
            $post = Post::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'image_url' => $imagePath ? asset('storage/' . $imagePath) : null,
                'scheduled_time' => isset($data['scheduled_time']) ? $data['scheduled_time'] : null ,
                'status' => empty($data['scheduled_time']) ? Post::STATUS_DRAFT : Post::STATUS_SCHEDULED ,
                'user_id' => $user->id,
            ]);
            $platformData = collect($data['platform_ids'])->mapWithKeys(fn($id) => [$id => ['platform_status' => PostPlatform::STATUS_PENDING]]);
            $post->platforms()->attach($platformData);


            return $post->load('platforms');
        });
    }

    /**
     * Update an existing post.
     */
    public function update(Post $post, array $data ,User $user)
    {   
        //check if post user_id is Auth->id
        if($user->id != $post->user_id){
            throw new \Exception('You can\'t update this post.');
        }
        return DB::transaction(function () use ($post, $data , $user) {

            //Rate Limit Check: if scheduling
            $isScheduling = !empty($data['scheduled_time']);
            $newDate = $isScheduling ? \Carbon\Carbon::parse($data['scheduled_time'])->toDateString() : null;
            $oldDate = $post->scheduled_time ? \Carbon\Carbon::parse($post->scheduled_time)->toDateString() : null;

            if ($isScheduling && ($post->status !== Post::STATUS_SCHEDULED || $newDate !== $oldDate)) {
                $scheduledCount = \App\Models\Post::where('user_id', $user->id)
                    ->where('status', \App\Models\Post::STATUS_SCHEDULED)
                    ->whereDate('scheduled_time', $newDate)
                    ->where('id', '!=', $post->id)
                    ->count();

                if ($scheduledCount >= 10) {
                    throw new \Exception('You have reached the limit of 10 scheduled posts for this day.');
                }
            }

            //check active platforms
            $activePlatformIds = $user->activePlatforms()
                ->pluck('platforms.id')
                ->toArray();

            $selectedPlatformIds = $data['platform_ids'];
            $invalidPlatforms = array_diff($selectedPlatformIds, $activePlatformIds);

            if (count($invalidPlatforms)) {
                throw new \Exception('One or more selected platforms are not active for this user.');
            }

            $post->title = $data['title'];
            $post->content = $data['content'];
            $post->scheduled_time =  isset($data['scheduled_time']) ? $data['scheduled_time'] : null ;
            $post->status = empty($data['scheduled_time']) ? Post::STATUS_DRAFT : Post::STATUS_SCHEDULED;
            if (isset($data['remove_image']) && $post->image_url) {
                //remove stored image
                Storage::disk('public')->delete(str_replace('/storage/', '', $post->image_url));
                $post->image_url = null;
            }
            if (isset($data['image'])) {
                if ($post->image_url) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $post->image_url));
                }

                $imagePath = $data['image']->store('posts', 'public');
                $post->image_url = asset('storage/' . $imagePath);
            }
            $post->save();
            
            if (isset($data['platform_ids'])) {
                $platformData = collect($data['platform_ids'])->mapWithKeys(function ($id) {
                    return [$id => ['platform_status' => PostPlatform::STATUS_PENDING]];
                });

                $post->platforms()->sync($platformData);
            }

            return $post->load('platforms');
        });
    }

    /**
     * Delete a post.
     */
    public function delete(Post $post,int $userId)
    {
        if ($post->user_id !== $userId) {
            throw new \Exception('You are not authorized to delete this post.');
        }

        return DB::transaction(function () use ($post) {
            // delete image from storage
            if ($post->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $post->image_url));
            }
            $post->platforms()->detach();

            $post->delete();

            return true;
        });
    }

    public function analytics($user)
    {
        $byPlatform = PostPlatform::select('platform_id', DB::raw('count(*) as total'))
            ->whereHas('post', fn($q) => $q->where('user_id', $user->id))
            ->groupBy('platform_id')
            ->with('platform')
            ->get();

        
        $successRate = PostPlatform::whereHas('post', fn($q) => $q->where('user_id', $user->id))
            ->select(
                DB::raw("count(case when platform_status = '".PostPlatform::STATUS_PUBLISHED."' then 1 end) as published"),
                DB::raw("count(case when platform_status = '".PostPlatform::STATUS_FAILED."' then 1 end) as failed"),
                DB::raw("count(case when platform_status = '".PostPlatform::STATUS_PENDING."' then 1 end) as pending"),
                DB::raw("count(*) as total")
            )
            ->first();

        $postStatusCounts = Post::where('user_id', $user->id)
        ->select(
            DB::raw("count(case when status = '".Post::STATUS_DRAFT."' then 1 end) as draft"),
            DB::raw("count(case when status = '".Post::STATUS_SCHEDULED."' then 1 end) as scheduled"),
            DB::raw("count(case when status = '".Post::STATUS_PUBLISHED."' then 1 end) as published"),
            DB::raw("count(*) as total")
        )
        ->first();

        return [
            'by_platform' => $byPlatform,
            'success_rate' => $successRate,
            'post_status_counts' => $postStatusCounts,
        ];
    }


   
    

}
