<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course; // Import Course model for relationship
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class CourseMaterial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'content',
        'parent_id',
        'order',
        'type',
    ];
    

    /**
     * Get the course that this material belongs to.
     * Defines an inverse one-to-many relationship.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function parent()
    {
        // A material belongs to one parent material, using the parent_id foreign key
        return $this->belongsTo(CourseMaterial::class, 'parent_id');
    }

    /**
     * Get the children (sub-materials) of this material.
     * Defines the one-to-many self-referencing relationship.
     */
    public function children()
    {
        // A material can have many child materials, linked by their parent_id
        // Order children by the 'order' column
        return $this->hasMany(CourseMaterial::class, 'parent_id')->orderBy('order', 'asc');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

}