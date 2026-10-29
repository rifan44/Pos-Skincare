<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $query = Debt::query()->with('creator')->latest('due_date')->latest('id');

        if ($request->filled('direction')) {
            $query->where('direction', $request->string('direction'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('party_name', 'like', '%'.$search.'%')
                    ->orWhere('reference', 'like', '%'.$search.'%');
            });
        }

        $debts = $query->paginate(15)->withQueryString();
        $summary = [
            'payable' => Debt::where('direction', 'payable')->whereIn('status', ['open', 'partial'])->sum(DB::raw('amount - paid_amount')),
            'receivable' => Debt::where('direction', 'receivable')->whereIn('status', ['open', 'partial'])->sum(DB::raw('amount - paid_amount')),
        ];

        return view('pages.debts.index', compact('debts', 'summary'));
    }

    public function create()
    {
        return view('pages.debts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'direction' => ['required', 'in:payable,receivable'],
            'party_type' => ['required', 'in:supplier,customer,other'],
            'party_name' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1', 'max:9223372036854775807'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $validated['created_by'] = $request->user()->id;
        $validated['paid_amount'] = 0;
        $validated['status'] = 'open';

        $debt = Debt::create($validated);

        return redirect()->route('debts.show', $debt)->with('success', 'Utang/piutang berhasil dicatat.');
    }

    public function show(Debt $debt)
    {
        $debt->load(['payments.creator', 'creator']);

        return view('pages.debts.show', compact('debt'));
    }

    public function storePayment(Request $request, Debt $debt)
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:9223372036854775807'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,transfer,qris,debit'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($debt, $validated, $request) {
            $lockedDebt = Debt::query()->lockForUpdate()->findOrFail($debt->id);
            $remaining = $lockedDebt->remaining_amount;

            if ((int) $validated['amount'] > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => 'Pembayaran tidak boleh melebihi sisa saldo.',
                ]);
            }

            $lockedDebt->payments()->create([
                ...$validated,
                'created_by' => $request->user()->id,
            ]);

            $paidAmount = (int) $lockedDebt->paid_amount + (int) $validated['amount'];
            $lockedDebt->update([
                'paid_amount' => $paidAmount,
                'status' => $paidAmount >= $lockedDebt->amount ? 'paid' : 'partial',
            ]);
        });

        return redirect()->route('debts.show', $debt)->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function cancel(Debt $debt)
    {
        if ($debt->paid_amount > 0) {
            throw ValidationException::withMessages([
                'debt' => 'Catatan yang sudah memiliki pembayaran tidak dapat dibatalkan.',
            ]);
        }

        $debt->update(['status' => 'cancelled']);

        return redirect()->route('debts.index')->with('success', 'Catatan utang/piutang dibatalkan.');
    }
}
