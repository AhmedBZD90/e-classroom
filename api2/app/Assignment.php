<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    /**
     * Attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
      'due_date',
      'course_id',
      'user_id'
    ];

    /**
     * Format the due date appropriately
     * 
     * @param Date $value 
     * @return Date
     */
    public function setDueDateAttribute($value)
    {
      $this->attributes['due_date'] = $value;
    }

    /**
     * Set the relationship between the Assignment 
     * model and the Course model
     * 
     * @return Response
     */
    public function course() : BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Set the relationship between the Assignment 
     * model and the User model
     * 
     * @return Response
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
