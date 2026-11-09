<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'address', 'is_walk_in', 'is_active'];

    protected function casts(): array
    {
        return ['is_walk_in' => 'boolean', 'is_active' => 'boolean'];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public static function walkIn(): self
    {
        return static::where('is_walk_in', true)->firstOrFail();
    }
}
