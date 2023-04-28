<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoucherRequest;
use App\Interfaces\ExportVoucherInterface;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class VoucherController extends Controller implements ExportVoucherInterface
{
    protected $modelService;

    public function __construct(VoucherService $modelService)
    {
        $this->modelService = $modelService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function preIndex()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        switch ($userType) {
            case 'user':
                $groups = [$this->modelService->getUserGroup($userId)->name ?? null];
                
                break;
            case 'group_admin':
                $groups = $this->modelService->getAdminGroup($userId, true);
                
                break;
            case 'super_admin':
                $groups = $this->modelService->getAllGroup(true);
                
                break;
            default:
                $groups = null;
        }

        return Inertia::render('Vouchers/PreIndex', [
            'groups' => $groups,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        
        $allowedToAdd = false;
        $allowedToDelete = false;
        $showOwner = false;
        $showGroup = false;
        $addIsOnLimit = false;

        if ($id == 0) {
            switch ($userType) {
                case 'user':
                    $allowedToAdd = true;
                    $allowedToDelete = true;
    
                    $userGroup = $this->modelService->getUserGroup($userId);
                    $userVouchers = $this->modelService->getUserVoucher($userId);
    
                    $vouchers = $userVouchers['vouchers'];
                    $addIsOnLimit = $userVouchers['limit'];
                    
                    break;
                case 'group_admin':
                    $showOwner = true;
                    $showGroup = true;
                    
                    $userVouchersByGroups = $this->modelService->getUserVoucherByGroups($userId);
                    $vouchers = $userVouchersByGroups['vouchers'];
                    $groups = $this->modelService->getAdminGroup($userId);
                    
                    break;
                case 'super_admin':
                    $showOwner = true;
                    $showGroup = true;
                    
                    $userVouchersByGroups = $this->modelService->getAllUserVouchers();
                    $vouchers = $userVouchersByGroups['vouchers'];
                    $groups = $this->modelService->getAllGroup();
                    
                    break;
                default:
                    $vouchers = null;
            }
        } else {
            switch ($userType) {
                case 'user':
                    $allowedToAdd = true;
                    $allowedToDelete = true;
    
                    $userGroup = $this->modelService->getUserGroup($userId);
                    $userVouchers = $this->modelService->getUserVoucher($userId);
    
                    $vouchers = $userVouchers['vouchers'];
                    $addIsOnLimit = $userVouchers['limit'];
                    
                    break;
                case 'group_admin':
                    $showOwner = true;
                    $showGroup = true;
                    
                    $userVouchersByGroups = $this->modelService->getUserVoucherByGroups($userId, $id);
                    $vouchers = $userVouchersByGroups['vouchers'];
                    $groups = $this->modelService->getAdminGroup($userId);
                    
                    break;
                case 'super_admin':
                    $showOwner = true;
                    $showGroup = true;
                    
                    $userVouchersByGroups = $this->modelService->getAllUserVouchers($id);
                    $vouchers = $userVouchersByGroups['vouchers'];
                    $groups = $this->modelService->getAllGroup();
                    
                    break;
                default:
                    $vouchers = null;
            }
        }

        return Inertia::render('Vouchers/Index', [
            'vouchers' => $vouchers,
            'allowedToAdd' => $allowedToAdd,
            'addIsOnLimit' => $addIsOnLimit,
            'allowedToDelete' => $allowedToDelete,
            'showOwner' => $showOwner,
            'showGroup' => $showGroup,
            'group' => $userGroup ?? $groups ?? null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Vouchers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VoucherRequest $request)
    {
        $store = $this->modelService->store($request);
        if (!$store['success']) {
            Log::error('Creating voucher failed', [
                'Message' => $store['message']->getMessage(),
                'Error' => $store
            ]);

            return redirect()->route('vouchers-create')->withErrors($store['message']->getMessage(), 'error');
        }

        return redirect()->route('vouchers-preindex');
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
        $remove = $this->modelService->remove($id);
        if (!$remove['success']) {
            Log::error('Deleting voucher failed', [
                'Message' => $remove['message']->getMessage(),
                'Error' => $remove
            ]);

            return redirect()->route('vouchers-preindex')->withErrors($remove['message']->getMessage(), 'error');
        }

        return response(["status" => $remove['success']], 200);
    }

    public function export($id)
    {
        $userType = Auth::user()->user_type;
        
        switch ($userType) {
            case 'group_admin':
                $fileName = $this->modelService->exportUserVoucherByGroups($id);

                break;
            case 'super_admin':
                $fileName = $this->modelService->exportAllUserVouchers($id);
                
                break;
            default:
                $fileName = null;
        }
        
        $path = storage_path(str_replace("'", "", ("app\\")) . $fileName);

        $results = [
            'success' => ($fileName ?? null) ? true : false,
            'path' => ($fileName ?? null) ? $path : false,
            'user' => Auth::user()->email
        ];

        Log::info('Export Log.', [
            'Data' => $results
        ]);

        return response()->download($path);
    }
}
