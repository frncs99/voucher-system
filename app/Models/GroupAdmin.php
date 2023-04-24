<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupAdmin extends Model
{
    use HasFactory;

    protected $table = 'group_admins';
    protected $primaryKey = 'group_admin_id';

    protected $fillable = [
        'group_id',
        'user_admin_id',
        'is_active'
    ];

    public function group()
    {
        return $this->hasOne(Group::class, 'group_id', 'group_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_admin_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
