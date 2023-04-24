<?php

namespace App\Interfaces;

use App\Http\Requests\NewAdminRequest;

interface GroupAssigningAdminInterface {
    public function getAdmin(int $groupId);
    public function assignAdmin(int $groupAdminId);
    public function createNewAdmin(int $groupId);
    public function storeNewAdmin(NewAdminRequest $request, $id);
}
