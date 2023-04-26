<?php

namespace App\Interfaces;

use App\Http\Requests\NewMemberRequest;

interface GroupAssigningMemberInterface {
    public function getMembers(int $groupId);
    public function assignMember(int $groupMemberId);
    public function createNewMember(int $groupId);
    public function checkCurrentGroup(int $userId);
    public function storeNewMember(NewMemberRequest $request, $id);
}
