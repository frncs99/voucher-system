<?php

namespace App\Interfaces;

interface UserCountersInterface {
    public function getUserCount(int $id, string $type): int;
}
