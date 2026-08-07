<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SponsoredChild extends Model
{
    protected $fillable = [
        'reference_code',
        'first_name',
        'last_name',
        'date_of_birth',
        'location',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function sponsorMatches(): HasMany
    {
        return $this->hasMany(DonorSponsorMatch::class);
    }
}
