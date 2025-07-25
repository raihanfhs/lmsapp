<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// If you treat this strictly as a pivot model and need pivot-specific features,
// you might extend Pivot instead of Model:
// use Illuminate\Database\Eloquent\Relations\Pivot;
// class Enrollment extends Pivot

class Enrollment extends Model // Using Model is usually fine
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
    ];
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}