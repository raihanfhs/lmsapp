<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     * Kita hanya perlu created_at, jadi kita set updated_at menjadi false.
     *
     * @var bool
     */
    public const UPDATED_AT = null;
}