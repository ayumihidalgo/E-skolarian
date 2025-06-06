<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role_name',
        'role',
        'action',
        'target',
        'description',
        'ip_address'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
