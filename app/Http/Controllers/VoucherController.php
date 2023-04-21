<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoucherRequest;
use App\Interfaces\ExportVoucherInterface;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
    public function index()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        
        $allowedToAdd = false;
        $allowedToDelete = false;
        $showOwner = false;
        $showGroup = false;
        $addIsOnLimit = false;

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
                $groups = $userVouchersByGroups['groups'];
                
                break;
            case 'super_admin':
                $showOwner = true;
                $showGroup = true;
                
                $userVouchersByGroups = $this->modelService->getAllUserVouchers();
                $vouchers = $userVouchersByGroups['vouchers'];
                
                break;
            default:
                $vouchers = null;
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

        return redirect()->route('vouchers-index');
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

            return redirect()->route('vouchers-index')->withErrors($remove['message']->getMessage(), 'error');
        }

        return redirect()->route('vouchers-index');
    }

    public function export()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        switch ($userType) {
            case 'group_admin':
                $fileName = $this->modelService->exportUserVoucherByGroups($userId);

                break;
            case 'super_admin':
                $fileName = $this->modelService->exportAllUserVouchers();
                
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
