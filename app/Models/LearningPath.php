<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LearningPath extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'slug',
        'is_active',
    ];

    /**
     * The courses that belong to the learning path.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_learning_path')
                    ->withPivot('order') // Mengambil kolom 'order' dari tabel pivot
                    ->orderBy('order');  // Mengurutkan kursus berdasarkan kolom 'order'
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'learning_path_user');
    }
}