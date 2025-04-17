<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.reports');
    }

    public function generate(Request $request)
    {
        try {
            $request->validate([
                'filter_type' => 'required|in:week,month,year',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'department' => 'required|string'
            ]);

            $stocks = DB::table('stocks')
                ->select('control_number', 'product_name', 'category', 'quantity', 'price', 'department', 'branch', 'created_at')
                ->whereBetween('created_at', [$request->start_date, $request->end_date])
                ->where('department', $request->department)
                ->get();

            $items = $stocks->map(function ($stock) {
                return [
                    'stock_no' => $stock->control_number,
                    'unit' => $stock->category,
                    'description' => $stock->product_name,
                    'price' => number_format($stock->price, 2),
                    'quantity' => $stock->quantity,
                    'department' => $stock->department,
                    'remarks' => ''
                ];
            });

            $data = [
                'items' => $items,
                'signatures' => [
                    'requested_by' => 'JOHN DOE',
                    'approved_by' => 'JANE SMITH',
                    'issued_by' => 'MARK JOHNSON'
                ],
                'purpose' => 'For office use'
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Report generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report'], 500);
        }
    }
}
