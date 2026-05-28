<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Itinerary;
use App\Services\ExpenseService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $expenseService) {}

    public function index(Itinerary $itinerary)
    {
        $this->authorize('view', $itinerary);
        if (!$itinerary->is_paid) {
            return redirect()->route('itineraries.show', $itinerary)
                ->with('error', 'This is a premium feature. Please unlock this itinerary to manage expenses.');
        }
        $expenses  = $itinerary->expenses()->latest()->paginate(15);
        $dashboard = $this->expenseService->getDashboard($itinerary);
        return view('expenses.index', compact('itinerary', 'expenses', 'dashboard'));
    }

    public function store(Request $request, Itinerary $itinerary)
    {
        $this->authorize('update', $itinerary);
        if (!$itinerary->is_paid) {
            abort(403, 'This is a premium feature. Please unlock this itinerary first.');
        }

        $validated = $request->validate([
            'category'       => 'required|string',
            'amount'         => 'required|numeric|min:0.01',
            'currency'       => 'nullable|string|max:3',
            'description'    => 'nullable|string|max:200',
            'expense_date'   => 'required|date',
            'payment_method' => 'nullable|string',
            'receipt_image'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')
                ->store('receipts', 'public');
        }

        $expense = $itinerary->expenses()->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        // Update itinerary spent
        $total = $itinerary->expenses()->sum('amount');
        $itinerary->update(['spent' => $total]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'expense' => $expense]);
        }

        return back()->with('success', 'Expense logged!');
    }

    public function destroy(Itinerary $itinerary, Expense $expense)
    {
        $this->authorize('update', $itinerary);
        if (!$itinerary->is_paid) {
            abort(403, 'This is a premium feature. Please unlock this itinerary first.');
        }
        $expense->delete();

        $total = $itinerary->expenses()->sum('amount');
        $itinerary->update(['spent' => $total]);

        return back()->with('success', 'Expense deleted.');
    }

    public function dashboard(Request $request)
    {
        // Overall spending across all itineraries
        $itineraries = auth()->user()->itineraries()->with('expenses')->get();
        $totalSpent  = $itineraries->sum(fn($i) => $i->expenses->sum('amount'));
        $totalBudget = $itineraries->sum('budget');

        return view('expenses.dashboard', compact('itineraries', 'totalSpent', 'totalBudget'));
    }
}
