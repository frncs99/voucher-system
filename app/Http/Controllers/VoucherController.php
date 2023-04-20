<?php

namespace App\Http\Controllers;

use App\Models\GroupAdmin;
use App\Models\GroupMember;
use App\Models\Voucher;
use Error;
use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        switch ($userType) {
            case 'user':
                $allowedToAdd = true;
                $allowedToDelete = true;
                $showOwner = false;
                $showGroup = false;

                $group = GroupMember::select(
                        'groups.name'
                    )
                    ->where('user_id', $userId)
                    ->where('is_active', 1)
                    ->join(
                        'groups',
                        'groups.group_id',
                        '=',
                        'group_members.group_id'
                    )
                    ->first();

                $vouchers = Voucher::select(
                        'vouchers.created_at',
                        'vouchers.voucher_id',
                        'vouchers.code',
                    )
                    ->where('vouchers.user_id', $userId)
                    ->where('vouchers.deleted_at', null);
                
                if ($vouchers->count() >= 10) {
                    $addIsOnLimit = true;
                }

                $vouchers = $vouchers->paginate(5);
                break;
            case 'group_admin':
                $allowedToAdd = false;
                $allowedToDelete = false;
                $showOwner = true;
                $showGroup = true;
                $vouchers = GroupAdmin::select(
                        'vouchers.created_at',
                        'vouchers.voucher_id',
                        'vouchers.code',
                        'users.email',
                        'users.name',
                        'groups.name as group_name',
                        'group_members.is_active',
                    )
                    ->where('group_members.is_active', 1)
                    ->where('group_admins.is_active', 1)
                    ->where('user_admin_id', $userId)
                    ->join(
                        'groups',
                        'groups.group_id',
                        '=',
                        'group_admins.group_id'
                    )
                    ->join(
                        'group_members',
                        'group_members.group_id',
                        '=',
                        'group_admins.group_id'
                    )
                    ->join(
                        'vouchers',
                        'vouchers.user_id',
                        '=',
                        'group_members.user_id'
                    )
                    ->join(
                        'users',
                        'users.id',
                        '=',
                        'vouchers.user_id'
                    )
                    ->paginate(5);
                break;
            case 'super_admin':
                $allowedToAdd = false;
                $allowedToDelete = false;
                $showOwner = true;
                $showGroup = true;
                $vouchers = Voucher::select(
                        'vouchers.created_at',
                        'vouchers.voucher_id',
                        'vouchers.code',
                        'groups.name as group_name',
                        'group_members.is_active',
                        'users.email',
                        'users.name',
                    )
                    ->where('vouchers.deleted_at', null)
                    ->join(
                        'group_members',
                        'group_members.user_id',
                        '=',
                        'vouchers.user_id'
                    )
                    ->join(
                        'groups',
                        'groups.group_id',
                        '=',
                        'group_members.group_id'
                    )
                    ->join(
                        'users',
                        'users.id',
                        '=',
                        'vouchers.user_id'
                    )
                    ->paginate(5);
                break;
            default:
                $allowedToAdd = false;
                $addIsOnLimit = false;
                $allowedToDelete = false;
                $showOwner = false;
                $showGroup = false;
                $vouchers = null;
        }

        return Inertia::render('Vouchers/Index', [
            'vouchers' => $vouchers,
            'allowedToAdd' => $allowedToAdd,
            'addIsOnLimit' => $addIsOnLimit ?? false,
            'allowedToDelete' => $allowedToDelete,
            'showOwner' => $showOwner,
            'showGroup' => $showGroup,
            'group' => $group ?? null
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
        try {
            $voucher = Voucher::find($id);
            $voucher->delete();
        } catch (Error $ex) {
            Log::error('Deleting voucher failed', [
                'Message' => $ex->getMessage(),
                'Error' => $ex
            ]);

            return redirect()->route('vouchers-index')->withErrors($ex->getMessage(), 'error');
        }

        return redirect()->route('vouchers-index');
    }
}
