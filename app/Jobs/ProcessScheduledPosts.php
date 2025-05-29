<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostPlatform;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessScheduledPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $duePosts = Post::with('platforms')
            ->where('status', Post::STATUS_SCHEDULED)
            ->where('scheduled_time', '<=', now())
            ->get();

        foreach ($duePosts as $post) {
            foreach ($post->platforms as $platform) {
                // 
                Log::info("Publishing post #{$post->id} to platform: {$platform->name}");

                // update platform status
                $post->platforms()->updateExistingPivot($platform->id, [
                    'platform_status' => PostPlatform::STATUS_PUBLISHED,
                ]);
            }

            // update post status
            $post->update(['status' => Post::STATUS_PUBLISHED]);
        }
    }
}
