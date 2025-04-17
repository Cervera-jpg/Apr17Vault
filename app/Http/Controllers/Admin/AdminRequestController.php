<?php

namespace App\Http\Controllers\Admin;

use App\Models\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.role:admin');
    }

    public function index()
    {
        // Get unique requests with aggregated information
        $requests = Request::with(['user', 'approver'])
                         ->select([
                             'request_id',
                             'department',
                             'branch',
                             'status',
                             'user_id',
                             'created_at',
                             DB::raw('COUNT(*) as item_count'),
                             DB::raw('MAX(id) as id'),
                             DB::raw('MAX(approved_by) as approved_by'),
                             DB::raw('MAX(approved_at) as approved_at'),
                         ])
                         ->groupBy('request_id', 'department', 'branch', 'status', 'user_id', 'created_at')
                         ->latest('created_at')
                         ->get();

        // Get stock request items from StockRequestItem model
        $stockItems = \App\Models\StockRequestItem::whereIn('stock_request_id', $requests->pluck('request_id'))
                    ->get()
                    ->groupBy('stock_request_id');
        
        // Attach items to each request
        foreach ($requests as $request) {
            $request->stockItems = $stockItems[$request->request_id] ?? collect();
        }

        // For backwards compatibility with existing view
        $requestItems = $stockItems;

        return view('Admin.adminviewreq', compact('requests', 'requestItems'));
    }

    public function updateStatus(HttpRequest $request, $requestId)
    {
        try {
            \Log::info('Starting updateStatus', [
                'requestId' => $requestId,
                'input' => $request->all()
            ]);

            // Find the main request
            $mainRequest = Request::where('request_id', $requestId)->first();
            \Log::info('Main request found', ['mainRequest' => $mainRequest]);

            if (!$mainRequest) {
                return redirect()->back()->with('error', 'Request not found.');
            }

            $validated = $request->validate([
                'status' => 'required|in:approved,rejected',
                'remarks' => 'required|string|max:255'
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            // Start a transaction
            DB::beginTransaction();
            
            try {
                // Update all associated requests with the same request_id
                $affected = DB::table('requests')
                    ->where('request_id', $requestId)
                    ->update([
                        'status' => $validated['status'],
                        'remarks' => $validated['remarks'],
                        'approved_by' => Auth::id(),
                        'approved_at' => now()
                    ]);

                \Log::info('Requests updated', ['affected' => $affected]);

                // If requests are approved, update inventory and prepare for stock creation
                if ($validated['status'] === 'approved') {
                    // Get stock items for this request
                    $stockItems = \App\Models\StockRequestItem::where('stock_request_id', $requestId)->get();
                    \Log::info('Stock items found', ['count' => $stockItems->count(), 'items' => $stockItems]);
                    
                    // If we have stock items, use those
                    if ($stockItems->count() > 0) {
                        foreach ($stockItems as $item) {
                            // Find the corresponding inventory item
                            $inventoryItem = \App\Models\SuppliesInventory::where('product_name', $item->product_name)
                                ->where('unit_type', $item->category)
                                ->first();

                            \Log::info('Processing inventory item', [
                                'product' => $item->product_name,
                                'found' => $inventoryItem ? true : false,
                                'inventory' => $inventoryItem
                            ]);

                            if ($inventoryItem) {
                                // Check if there's enough quantity
                                if ($inventoryItem->quantity < $item->quantity) {
                                    DB::rollBack();
                                    return redirect()->back()->with('error', "Insufficient stock for {$item->product_name}. Available: {$inventoryItem->quantity}, Requested: {$item->quantity}");
                                }

                                // Decrease inventory quantity
                                $inventoryItem->quantity -= $item->quantity;
                                $inventoryItem->save();
                            }
                        }

                        // Prepare data for stock creation
                        $requestData = $stockItems->map(function($item) use ($mainRequest) {
                            return [
                                'product_name' => $item->product_name,
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'department' => $mainRequest->department,
                                'branch' => $mainRequest->branch,
                                'category' => $item->category,
                            ];
                        })->toArray();

                        \Log::info('Prepared request data for session', ['requestData' => $requestData]);

                        // Store request data in session and redirect
                        session(['request_data' => $requestData]);
                        \Log::info('Session data stored', ['session' => session()->all()]);
                        
                        DB::commit();
                        
                        return redirect()->route('admin.stocks.create')->with('success', 'Request approved successfully and inventory updated.');
                    } else {
                        // Legacy support for old requests without stock items
                        $oldRequests = Request::where('request_id', $requestId)->get();
                        \Log::info('Using legacy requests', ['count' => $oldRequests->count(), 'requests' => $oldRequests]);

                        foreach ($oldRequests as $oldRequest) {
                            // Find the corresponding inventory item
                            $inventoryItem = \App\Models\SuppliesInventory::where('product_name', $oldRequest->product_name)
                                ->where('unit_type', $oldRequest->category)
                                ->first();

                            if ($inventoryItem) {
                                // Check if there's enough quantity
                                if ($inventoryItem->quantity < $oldRequest->quantity) {
                                    DB::rollBack();
                                    return redirect()->back()->with('error', "Insufficient stock for {$oldRequest->product_name}. Available: {$inventoryItem->quantity}, Requested: {$oldRequest->quantity}");
                                }

                                // Decrease inventory quantity
                                $inventoryItem->quantity -= $oldRequest->quantity;
                                $inventoryItem->save();
                            }
                        }

                        $requestData = $oldRequests->map(function($request) {
                            return [
                                'product_name' => $request->product_name,
                                'quantity' => $request->quantity,
                                'price' => $request->price,
                                'department' => $request->department,
                                'branch' => $request->branch,
                                'category' => $request->category,
                            ];
                        })->toArray();

                        \Log::info('Prepared legacy request data for session', ['requestData' => $requestData]);

                        // Store request data in session and redirect
                        session(['request_data' => $requestData]);
                        \Log::info('Legacy session data stored', ['session' => session()->all()]);
                        
                        DB::commit();
                        
                        return redirect()->route('admin.stocks.create')->with('success', 'Request approved successfully and inventory updated.');
                    }
                }

                // If rejected, just commit and go back
                DB::commit();
                return redirect()->back()->with('success', 'Request has been rejected successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Error in update:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function approve(Request $request)
    {
        return $this->updateStatus($request, [
            'status' => 'approved',
            'remarks' => request('remarks')
        ]);
    }

    public function reject(Request $request)
    {
        return $this->updateStatus($request, [
            'status' => 'rejected',
            'remarks' => request('remarks')
        ]);
    }
}
