<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'vouchers';
    protected $primaryKey = 'voucher_id';

    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
}
