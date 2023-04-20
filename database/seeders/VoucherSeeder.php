<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('user_type', 'user')->pluck('id')->toArray();

        foreach (range(0, 29) as $i) {
            $code = uniqid();
            $userId = $users[array_rand($users)];

            $voucher = Voucher::firstOrNew(['code' => $code]);
            if (!isset($voucher->user_id)) {
                $voucher->user_id = $userId;

                $count = Voucher::where('user_id', $userId)
                    ->where('deleted_at', null)
                    ->count();

                if ($count < 10) {
                    $voucher->save();
                }
            }

        }
    }
}
