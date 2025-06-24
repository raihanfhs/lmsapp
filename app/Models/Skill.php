<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Import User model for the relationship
use Illuminate\Support\Str; // For generating slugs, if you choose to

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * The users (teachers) that possess this skill.
     * Defines a many-to-many relationship.
     */
    public function users()
    {
        // Assuming you name the pivot table 'skill_user'
        return $this->belongsToMany(User::class, 'skill_user');
    }
}