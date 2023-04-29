<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupAdmin;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $modelService;

    public function __construct(UserService $modelService)
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
        if (Auth::user()->user_type == 'group_admin') {
            $groups = GroupAdmin::where('user_admin_id', Auth::user()->id)
                ->where('is_active', 1)
                ->pluck('group_id');
        } else {
            $groups = Group::pluck('group_id');
        }

        $users = User::select(
                'users.id',
                'users.name',
                'users.email',
                'users.created_at',
                'groups.name as group_name',
                'groups.group_id'
            )
            ->where('user_type', 'user')
            ->leftJoin(
                'group_members',
                'group_members.user_id',
                '=',
                'users.id'
            )
            ->leftJoin(
                'groups',
                'groups.group_id',
                '=',
                'group_members.group_id'
            );
        $users = $this->modelService->allWithPagination($users);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
