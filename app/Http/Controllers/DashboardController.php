<?php

namespace App\Http\Controllers;

use App\Interfaces\UserCountersInterface;
use App\Interfaces\UserGroupInterface;
use App\Interfaces\VoucherCountersInterface;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller implements VoucherCountersInterface, UserGroupInterface, UserCountersInterface
{
    protected $modelService;

    public function __construct(VoucherService $modelService)
    {
        $this->modelService = $modelService;
    }

    public function index()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        return Inertia::render('Dashboard', [
            'admin' => ($userType == 'user') ? 0 : 1,
            'voucherCount' => $this->getVoucherCount($userId, $userType),
            'ownGroups' => $this->getUserGroups($userId, $userType),
            'userCount' => $this->getUserCount($userId, $userType),
            'adminCount' => $this->getGroupAdminCount($userType)
        ]);
    }

    public function getVoucherCount($userId, $userType): int
    {
        switch ($userType) {
            case 'user':
                $counts = $this->modelService->getUserVoucherCounts($userId);
                
                break;
            case 'group_admin':
                $counts = $this->modelService->getUserVoucherByGroupsCounts($userId);
                
                break;
            case 'super_admin':
                $counts = $this->modelService->getAllUserVouchersCounts();
                
                break;
            default:
                $counts = 0;
        }

        return $counts ?? 0;
    }

    public function getUserGroups($userId, $userType): array
    {
        switch ($userType) {
            case 'user':
                $groups = [$this->modelService->getUserGroup($userId)->name];
                
                break;
            case 'group_admin':
                $groups = $this->modelService->getAdminGroup($userId);
                
                break;
            case 'super_admin':
                $groups = $this->modelService->getAllGroup();
                
                break;
            default:
                $groups = [];
        }

        return $groups ?? [];
    }

    public function getUserCount(int $userId, string $userType): int
    {
        switch ($userType) {
            case 'group_admin':
                $counts = $this->modelService->getUserByGroupsCounts($userId);
                
                break;
            case 'super_admin':
                $counts = $this->modelService->getAllUserCounts();
                
                break;
            default:
                $counts = 0;
        }

        return $counts ?? 0;
    }

    public function getGroupAdminCount(string $userType)
    {
        if ($userType != 'super_admin') {
            return 0;
        }
        
        return User::where('user_type', 'group_admin')->count();
    }
}
