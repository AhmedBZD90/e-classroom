<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
     protected $fillable = ['name', 'description', 'teacher_id', 'status', 'started_at', 'finished_at'];
    
    
     /**
     * @return BelongsTo
     */
    public function teacher() : BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }



    /**
     * @return HasMany
     */
    public function lessons() : HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Assignments that belong to this class
     * 
     */
    public function assignments() : HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
