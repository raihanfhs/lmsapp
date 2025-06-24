<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Import User model for the relationship

class Division extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        // 'slug', // Add if you included it in the migration and want it fillable
    ];

    /**
     * Get the users that belong to this division.
     * Defines a one-to-many relationship.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
