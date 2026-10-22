<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerProfile extends Model
{
    protected $table = 'trainer_profiles';

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'trainer_level',
        'avatar',
    ];

    protected function casts(): array
    {
        return [
            'trainer_level' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
