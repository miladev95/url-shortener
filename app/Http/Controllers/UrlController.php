<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrlShortenRequest;
use App\Models\Url;
use Illuminate\Support\Str;
use Miladev\ApiResponse\ApiResponse;

class UrlController extends Controller
{
    use ApiResponse;

    public function shorten(UrlShortenRequest $request)
    {
        $url = auth()->user()->urls()->create([
            'original_url' => $request->input('original_url'),
            'short_url' => Str::random(6),
            'visit_count' => 0,
        ]);

        return response()->json($url, 201);
    }

    public function convert($shortUrl)
    {
        $url = Url::where('short_url', $shortUrl)->first();

        if ($url) {
            // Increment visit count
            $url->increment('visit_count');

            // Record the visit
            $url->visits()->create([
                'visitor_ip' => request()->ip(),
            ]);

            return $this->successResponse(['original_url' => $url->original_url]);
        } else {
            return $this->failResponse(message: 'Url not found', statusCode: 404);
        }
    }
}
