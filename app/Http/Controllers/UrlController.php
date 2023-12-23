<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrlShortenRequest;
use App\Models\Url;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Miladev\ApiResponse\ApiResponse;

class UrlController extends Controller
{
    use ApiResponse;

    /**
     * shorting url
     * @param UrlShortenRequest $request
     * @return \Illuminate\Http\Response
     */
    public function shorten(UrlShortenRequest $request)
    {
        $url = auth()->user()->urls()->create([
            'original_url' => $request->input('original_url'),
            'short_url' => Str::random(6),
            'visit_count' => 0,
        ]);

        return $this->successResponse(data: $url, statusCode: 201);
    }

    /**
     * Convert short url with original one
     * @param $shortUrl
     * @return \Illuminate\Http\Response
     */
    public function convert($shortUrl)
    {
        try {
            // Check if the URL is in cache
            $url = Cache::remember("url:$shortUrl", now()->addMinutes(60), function () use ($shortUrl) {
                return Url::where('short_url', $shortUrl)->firstOrFail();
            });

            // Increment visit count
            $url->increment('visit_count');

            // Record the visit
            $url->visits()->create([
                'visitor_ip' => request()->ip(),
            ]);

            // Reload the URL to get the updated visit_count
            $url->refresh();

            return $this->successResponse(['original_url' => $url->original_url]);
        }catch (ModelNotFoundException $e) {
            return $this->failResponse(message: 'Url not found',statusCode: 404);
        }
    }

    /**
     * Show authenticated user urls and visits
     * @return \Illuminate\Http\Response
     */
    public function showUserUrls()
    {
        $user = auth()->user();

        // fetch user urls with visits
        $userUrls = $user->urls()->with('visits')->get();

        return $this->successResponse(data: ['user_urls' => $userUrls]);
    }
}
