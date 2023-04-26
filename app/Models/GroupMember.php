<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;

    protected $table = 'group_members';
    protected $primaryKey = 'group_member_id';

    protected $fillable = [
        'group_id',
        'user_id',
        'is_active'
    ];

    public function group()
    {
        return $this->hasOne(Group::class, 'group_id', 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
