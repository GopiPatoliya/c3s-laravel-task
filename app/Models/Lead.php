<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'status',
        'source',
        'assigned_to',
        'created_by',
        'notes',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
