<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'address',
        'thumbnail_id',
        'description',
        'price_max',
        'price_min',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Site $site) {
            $site->thumbnail()->delete();
        });
    }

    public function thumbnail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Image::class, 'id', 'thumbnail_id');
    }
}
