<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }
}
