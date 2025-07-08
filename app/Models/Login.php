<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Login extends Model
{
    use HasFactory;

    /**
     * Memberitahu Eloquent untuk tidak mengelola timestamps (created_at & updated_at) secara otomatis.
     * Ini adalah baris kunci untuk perbaikan.
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * Mendefinisikan relasi bahwa setiap record Login
     * dimiliki oleh satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}