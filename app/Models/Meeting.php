<?php
// File: app/Models/Meeting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model // <-- UBAH NAMA KELAS
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type', // <-- TAMBAHKAN INI
        'meeting_link',
        'location', // <-- TAMBAHKAN INI
        'meeting_datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}