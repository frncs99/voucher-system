<?php

namespace App\Services;

use App\Exports\VoucherExport;
use App\Http\Requests\VoucherRequest;
use App\Models\Group;
use App\Models\GroupAdmin;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\Voucher;
use App\Services\BaseService;
use Maatwebsite\Excel\Facades\Excel;

class VoucherService extends BaseService
{
    public function __construct(Voucher $model)
    {
        // pass the model to the parent constructor
        parent::__construct($model);

        // column use for sorting
        $this->defaultSortKey = ["voucher_id"] ;

        // column use for sorting
        $this->tableName = ["vouchers"] ;

        // validator class for the controller
        $this->requestValidator = new VoucherRequest();

        // model resource for response formatting
        $this->modelResource = "App\Http\Resources\VoucherResource";
    }

    public function paginateQueryWithoutModelResource($query, $count)
    {
        $this->modelResource = null;
        $query = $this->allWithPagination($query, $count);
        $this->modelResource = "App\Http\Resources\VoucherResource";

        return $query;
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

    public function getAdminGroup(int $groupAdminId)
    {
        return GroupAdmin::where('user_admin_id', $groupAdminId)
            ->where('is_active', 1)
            ->join(
                'groups',
                'groups.group_id',
                '=',
                'group_admins.group_id'
            )
            ->pluck('groups.name')
            ->toArray();
    }

    public function getAllGroup()
    {
        return Group::where('deleted_at', null)
            ->pluck('name')
            ->toArray();
    }

    public function getUserVoucher(int $userId)
    {
        $vouchers = $this->model::select(
                'vouchers.created_at',
                'vouchers.voucher_id',
                'vouchers.code',
            )
            ->where('vouchers.user_id', $userId)
            ->where('vouchers.deleted_at', null);
        
        if ($vouchers->count() >= 10) {
            $addIsOnLimit = true;
        }

        return [
            'vouchers' => $this->paginateQueryWithoutModelResource($vouchers, 5, '', null),
            'limit' => $addIsOnLimit ?? false
        ];
    }

    public function getUserVoucherByGroups(int $groupAdminId)
    {
        $vouchers = GroupAdmin::where('group_members.is_active', 1)
            ->where('group_admins.is_active', 1)
            ->where('user_admin_id', $groupAdminId)
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

        return [
            'vouchers' => $this->paginateQueryWithoutModelResource(
                    $vouchers->select(
                        'vouchers.created_at',
                        'vouchers.voucher_id',
                        'vouchers.code',
                        'users.email',
                        'users.name',
                        'groups.name as group_name',
                        'group_members.is_active',
                    ),
                    5,
                ),
            'groups' => $vouchers->select(
                    'groups.name',
                )
                ->groupBy('groups.name')
                ->pluck('groups.name'),
        ];
    }

    public function getAllUserVouchers()
    {
        $vouchers = $this->model::select(
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
            );

        return [
            'vouchers' => $this->paginateQueryWithoutModelResource($vouchers, 5)
        ];
    }

    public function getUserVoucherCounts(int $userId)
    {
        return $this->model::where('vouchers.user_id', $userId)
            ->where('vouchers.deleted_at', null)
            ->count();
    }

    public function getUserVoucherByGroupsCounts(int $groupAdminId)
    {
        return GroupAdmin::where('group_members.is_active', 1)
            ->where('group_admins.is_active', 1)
            ->where('user_admin_id', $groupAdminId)
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
            ->count();
    }

    public function getAllUserVouchersCounts()
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
            ->count();
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

    public function exportUserVoucherByGroups(int $groupAdminId)
    {
        $vouchers = GroupAdmin::select(
                'vouchers.created_at',
                'vouchers.voucher_id',
                'vouchers.code',
                'users.email',
                'users.name',
                'groups.name as group_name',
            )
            ->where('group_members.is_active', 1)
            ->where('group_admins.is_active', 1)
            ->where('user_admin_id', $groupAdminId)
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
            ->get();

        return $this->exportToExcel($vouchers, 'Vouchers_' . date('YmdHis'));
    }

    public function exportAllUserVouchers()
    {
        $vouchers = $this->model::select(
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
            ->get();

        return $this->exportToExcel($vouchers, 'Vouchers_' . date('YmdHis'));
    }
}
