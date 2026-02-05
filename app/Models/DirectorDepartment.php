<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectorDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department',
    ];

    /**
     * Get the user that this department assignment belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department label.
     */
    public function getDepartmentLabelAttribute()
    {
        return Van::DEPARTMENT_LABELS[$this->department] ?? $this->department;
    }
}
