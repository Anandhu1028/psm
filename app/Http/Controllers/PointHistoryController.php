<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Executive;
use App\Models\PointTransaction;
use App\Models\Zone;
use Illuminate\Http\Request;

class PointHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PointTransaction::with(['executive.company', 'executive.zone', 'dailyAudit', 'createdBy'])
            ->when($request->company_id,   fn($q) => $q->where('company_id',   $request->company_id))
            ->when($request->executive_id, fn($q) => $q->where('executive_id', $request->executive_id))
            ->when($request->category,     fn($q) => $q->where('category',     $request->category))
            ->when($request->type,         fn($q) => $q->where('type',         $request->type))
            ->when($request->date_from,    fn($q) => $q->whereDate('audit_date', '>=', $request->date_from))
            ->when($request->date_to,      fn($q) => $q->whereDate('audit_date', '<=', $request->date_to))
            ->orderByDesc('audit_date')
            ->orderByDesc('id');

        $transactions = $query->paginate(30)->withQueryString();

        $companies  = Company::active()->orderBy('name')->get();
        $executives = Executive::where('status', '!=', 'inactive')->orderBy('name')->get();

        // Summary stats for the filter result
        $summary = PointTransaction::when($request->company_id,   fn($q) => $q->where('company_id',   $request->company_id))
            ->when($request->executive_id, fn($q) => $q->where('executive_id', $request->executive_id))
            ->when($request->date_from,    fn($q) => $q->whereDate('audit_date', '>=', $request->date_from))
            ->when($request->date_to,      fn($q) => $q->whereDate('audit_date', '<=', $request->date_to))
            ->selectRaw("
                SUM(CASE WHEN type='credit' THEN points ELSE 0 END) as total_credits,
                SUM(CASE WHEN type='debit'  THEN points ELSE 0 END) as total_debits,
                COUNT(*) as total_transactions
            ")->first();

        return view('point_history.index', compact('transactions', 'companies', 'executives', 'summary'));
    }
}
