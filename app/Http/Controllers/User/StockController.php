<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userDepartment = $user->department;
        $userBranch = $user->branch;
        
        // User can only see stocks from their own department and branch
        $query = Stock::where('department', $userDepartment)
                      ->where('branch', $userBranch);
        
        $stocks = $query->get();
    
        return view('user.tables', compact('stocks'));
    }
}