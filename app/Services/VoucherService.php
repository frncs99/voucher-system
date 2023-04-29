<?php

namespace App\Services;

use App\Exports\VoucherExport;
use App\Models\Group;
use App\Models\GroupAdmin;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\Voucher;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class VoucherService extends BaseService
{
    public function __construct(Voucher $model)
    {
        // pass the model to the parent constructor
        parent::__construct($model);

        // column use for sorting
        $this->defaultSortKey = ["voucher_id"];

        // column use for sorting
        $this->tableName = ["vouchers"];

        // validator class for the controller
        // $this->requestValidator = new VoucherRequest();

        // model resource for response formatting
        $this->modelResource = "App\Http\Resources\VoucherResource";
    }

    public function getUserGroup(int $userId)
    {
        return GroupMember::select(
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
    }

    public function getAdminGroup(int $groupAdminId, $withId = false)
    {
        $groups = GroupAdmin::where('user_admin_id', $groupAdminId)
            ->where('is_active', 1)
            ->join(
                'groups',
                'groups.group_id',
                '=',
                'group_admins.group_id'
            );

        if ($withId) {
            return $groups->select('groups.name', 'groups.group_id')
                ->get();
        } else {
            return $groups->pluck('groups.name')
                ->toArray();
        }
    }

    public function getAllGroup($withId = false)
    {
        if ($withId) {
            return Group::select('name', 'group_id')
                ->get();
        } else {
            return Group::pluck('name')
                ->toArray();
        }
    }

    public function getUserVoucher(int $userId)
    {
        $vouchers = $this->model::select(
                'vouchers.created_at',
                'vouchers.voucher_id',
                'vouchers.code',
            )
            ->where('vouchers.user_id', $userId);
        
        if ($vouchers->count() >= 10) {
            $addIsOnLimit = true;
        }

        return [
            'vouchers' => $this->allWithPagination($vouchers),
            'limit' => $addIsOnLimit ?? false
        ];
    }

    public function queryVoucherByGroup(int $userAdminId, $specificGroupId = null)
    {
        return GroupAdmin::select(
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
            ->where('user_admin_id', $userAdminId)
            ->where(function ($query) use ($specificGroupId) {
                if ($specificGroupId != null) {
                    $query->where('groups.group_id', $specificGroupId);
                }
            })
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
            );
    }

    public function getUserVoucherByGroups(int $groupAdminId, $specificGroupId = null)
    {
        $vouchers = $this->queryVoucherByGroup($groupAdminId, $specificGroupId);

        return [
            'vouchers' => $this->allWithPagination($vouchers),
        ];
    }

    public function queryAllUserVoucher($specificGroupId = null)
    {
        return $this->model::select(
                'vouchers.created_at',
                'vouchers.voucher_id',
                'vouchers.code',
                'groups.name as group_name',
                'group_members.is_active',
                'users.email',
                'users.name',
            )
            ->where(function ($query) use ($specificGroupId) {
                if ($specificGroupId != null) {
                    $query->where('groups.group_id', $specificGroupId);
                }
            })
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
            );
    }

    public function getAllUserVouchers($specificGroupId = null)
    {
        $vouchers = $this->queryAllUserVoucher($specificGroupId);

        return [
            'vouchers' => $this->allWithPagination($vouchers)
        ];
    }

    public function getUserVoucherCounts(int $userId)
    {
        return $this->model::where('vouchers.user_id', $userId)
            ->count();
    }

    public function getUserVoucherByGroupsCounts(int $groupAdminId)
    {
        return $this->queryVoucherByGroup($groupAdminId)->count();
    }

    public function getAllUserVouchersCounts()
    {
        return $this->queryAllUserVoucher()->count();
    }

    public function getUserByGroupsCounts(int $userId)
    {
        return GroupAdmin::where('user_admin_id', $userId)
            ->where('group_admins.is_active', 1)
            ->where('group_members.is_active', 1)
            ->join(
                'group_members',
                'group_members.group_id',
                '=',
                'group_admins.group_id'
            )
            ->count();
    }

    public function getAllUserCounts()
    {
        return User::where('user_type', 'user')->count();
    }

    public function exportToExcel($query, $subject)
    {
        Excel::download(new VoucherExport($subject, $query), $subject . '.xlsx');
        Excel::store(new VoucherExport($subject, $query), $subject . '.xlsx');
        return $subject . '.xlsx';
    }

    public function exportUserVoucherByGroups($specificGroupId = 0)
    {
        $userId = Auth::user()->id;

        $vouchers = $this->queryVoucherByGroup($userId, $specificGroupId)->get();

        return $this->exportToExcel($vouchers, 'Vouchers_' . date('YmdHis'));
    }

    public function exportAllUserVouchers($specificGroupId = 0)
    {
        $vouchers = $this->queryAllUserVoucher($specificGroupId)->get();

        return $this->exportToExcel($vouchers, 'Vouchers_' . date('YmdHis'));
    }
}
