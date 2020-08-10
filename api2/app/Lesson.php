<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{

    protected  $fillable = ['title', 'content', 'course_id', 'status'];

    public function course() : BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    

    
}
