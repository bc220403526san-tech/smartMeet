<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleRequest extends Model
{
    protected $fillable = ['user_id', 'subject', 'message', 'requested_role', 'status', 'admin_note'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
