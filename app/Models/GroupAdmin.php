<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupAdmin extends Model
{
    use HasFactory;

    protected $table = 'group_admins';
    protected $primaryKey = 'group_admin_id';

    public function group()
    {
        return $this->hasOne(Group::class, 'group_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'user_admin_id');
    }
}
