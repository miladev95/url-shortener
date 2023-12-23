<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function shorten(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
        ]);

        $url = auth()->user()->urls()->create([
            'original_url' => $request->input('original_url'),
            'short_url' => Str::random(6),
            'visit_count' => 0,
        ]);

        return response()->json($url, 201);
    }

    public function convert($shortCode)
    {
        $url = Url::where('short_url', $shortCode)->firstOrFail();

        // Increment visit count
        $url->increment('visit_count');

        // Record the visit
        $url->visits()->create([
            'visitor_ip' => request()->ip(),
        ]);

        return response()->json(['original_url' => $url->original_url]);
    }
}
