<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupStoreRequest;
use App\Http\Requests\GroupUpdateRequest;
use App\Http\Requests\NewAdminRequest;
use App\Http\Requests\NewMemberRequest;
use App\Http\Resources\GroupAdminResource;
use App\Http\Resources\GroupMemberResource;
use App\Interfaces\GroupAssigningAdminInterface;
use App\Interfaces\GroupAssigningMemberInterface;
use App\Models\Group;
use App\Models\GroupAdmin;
use App\Models\GroupMember;
use App\Models\User;
use App\Services\GroupService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GroupController extends Controller implements GroupAssigningAdminInterface, GroupAssigningMemberInterface
{
    protected $modelService;

    public function __construct(GroupService $modelService)
    {
        $this->modelService = $modelService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->user_type == 'super_admin') {
            $groups = $this->modelService->allWithPagination(Group::withTrashed());

            return Inertia::render('Groups/Index', [
                'groups' => $groups,
            ]);
        } else {
            $groupIds = GroupAdmin::where('user_admin_id', Auth::user()->id)
                ->where('is_active', 1)
                ->pluck('group_id')
                ->toArray();
            $groups = $this->modelService->allWithPagination(Group::whereIn('group_id', $groupIds));

            return Inertia::render('Groups/Members/Index', [
                'groups' => $groups,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Groups/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupStoreRequest $request)
    {
        $store = $this->modelService->store($request);
        if (!$store['success']) {
            Log::error('Creating group failed', [
                'Message' => $store['message']->getMessage(),
                'Error' => $store
            ]);

            return redirect()->route('groups-create')->withErrors($store['message']->getMessage(), 'error');
        }

        return redirect()->route('groups-index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Inertia::render('Groups/Edit', [
            'group' => Group::find($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupUpdateRequest $request, $id)
    {
        $update = $this->modelService->store($request, $id);
        if (!$update['success']) {
            Log::error('Editing group failed', [
                'Message' => $update['message']->getMessage(),
                'Error' => $update
            ]);

            return redirect()->route('groups-edit')->withErrors($update['message']->getMessage(), 'error');
        }

        return redirect()->route('groups-index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteOrRestore = $this->modelService->deleteOrRestore($id);
        if (!$deleteOrRestore['success']) {
            Log::error('Deleting group failed', [
                'Message' => $deleteOrRestore['message']->getMessage(),
                'Error' => $deleteOrRestore
            ]);

            return response(["message" => $deleteOrRestore['message']->getMessage()], 422);
        }

        return response(["status" => $deleteOrRestore['success']], 200);
    }
    
    public function getAdmin(int $groupId)
    {
        $admins = GroupAdmin::where('group_id', $groupId)
            ->get();
        
        return Inertia::render('Groups/Admins', [
            'groupAdmins' => GroupAdminResource::collection($admins),
        ]);
    }

    public function assignAdmin(int $groupAdminId)
    {
        try {
            $admin = GroupAdmin::find($groupAdminId);
            $admin->is_active = ($admin->is_active == 1) ? 0 : 1;
            $admin->save();
        } catch (Exception $ex) {
            return response(["error" => $ex->getMessage()], 422);
        }

        return response(["status" => $admin->is_active], 200);
    }

    public function createNewAdmin(int $groupId)
    {
        $group = $this->modelService->find($groupId);
        $admins = User::select('id', 'email', 'name')->where('user_type', 'group_admin')->get();
        
        return Inertia::render('Groups/NewAdmins', [
            'group' => $group,
            'admins' => $admins,
        ]);
    }

    public function storeNewAdmin(NewAdminRequest $request, $id)
    {
        try {
            GroupAdmin::updateOrCreate(
                [
                    'group_id' => $id,
                    'user_admin_id' => $request->user_admin_id
                ],
                [
                    'is_active' => 1
                ]
            );
        } catch (Exception $ex) {
            return redirect()->route('groups-admin', $id)->withErrors($ex->getMessage(), 'error');
        }

        return redirect()->route('groups-admin', $id);
    }

    public function getMembers(int $groupId)
    {
        $members = GroupMember::where('group_id', $groupId)
            // ->where('is_active', 1)
            ->get();
            
        return Inertia::render('Groups/Members/Members', [
            'groupMembers' => GroupMemberResource::collection($members),
        ]);
    }

    public function assignMember(int $groupMemberId)
    {
        try {            
            $member = GroupMember::find($groupMemberId);
            $member->is_active = ($member->is_active == 1) ? 0 : 1;
            $member->save();
        } catch (Exception $ex) {
            return response(["message" => $ex->getMessage()], 422);
        }

        return response(["status" => $member->is_active], 200);
    }

    public function createNewMember(int $groupId)
    {
        $group = $this->modelService->find($groupId);
        $users = User::select('id', 'email', 'name')->where('user_type', 'user')->get();
        
        return Inertia::render('Groups/Members/NewMembers', [
            'group' => $group, 
            'users' => $users,
        ]);
    }

    public function checkCurrentGroup(int $userId)
    {
        return GroupMember::where('user_id', $userId)
            ->where('is_active', 1)
            ->join(
                'groups',
                'groups.group_id',
                '=',
                'group_members.group_id'
            )
            ->pluck('name');
    }

    public function storeNewMember(NewMemberRequest $request, $id)
    {
        try {
            $groupMember = GroupMember::firstOrNew(['user_id' => $request->user_id]);
            $groupMember->group_id = $id;
            $groupMember->is_active = 1;
            $groupMember->save();
        } catch (Exception $ex) {
            return redirect()->route('group-new-member', $id)->withErrors($ex->getMessage(), 'error');
        }

        return redirect()->route('group-member', $id);
    }
}
