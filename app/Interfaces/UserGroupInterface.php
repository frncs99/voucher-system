<?php

namespace App\Interfaces;

interface UserGroupInterface {
    public function getUserGroups(int $id, string $type): array;
}
