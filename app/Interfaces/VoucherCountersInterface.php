<?php

namespace App\Interfaces;

interface VoucherCountersInterface {
    public function getVoucherCount(int $id, string $type): int;
}
