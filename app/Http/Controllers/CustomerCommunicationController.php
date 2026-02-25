<?php

namespace App\Http\Controllers;

use App\Models\CustomerCommunication;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerCommunicationController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerCommunication::with(['customer', 'user']);

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $communications = $query->latest()->paginate(20);
        $customers = Customer::all();

        return view('customer-communications.index', compact('communications', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:whatsapp,email,visit,call',
            'notes' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ]);

        $validated['user_id'] = auth()->id();

        CustomerCommunication::create($validated);

        // Update customer last_contacted_at
        $customer = Customer::find($validated['customer_id']);
        if ($customer && $validated['completed_at']) {
            $customer->update(['last_contacted_at' => $validated['completed_at']]);
        }

        return redirect()->route('customer-communications.index')->with('success', 'Communication logged successfully');
    }

    public function edit(CustomerCommunication $customerCommunication)
    {
        $customerCommunication->load(['customer', 'user']);
        $customers = Customer::all();
        return view('customer-communications.edit', compact('customerCommunication', 'customers'));
    }

    public function update(Request $request, CustomerCommunication $customerCommunication)
    {
        $validated = $request->validate([
            'type' => 'required|in:whatsapp,email,visit,call',
            'notes' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ]);

        $customerCommunication->update($validated);

        // Update customer last_contacted_at
        if ($validated['completed_at']) {
            $customer = $customerCommunication->customer;
            $customer->update(['last_contacted_at' => $validated['completed_at']]);
        }

        return redirect()->route('customer-communications.index')->with('success', 'Communication updated successfully');
    }

    public function destroy(CustomerCommunication $customerCommunication)
    {
        $customerCommunication->delete();
        return redirect()->route('customer-communications.index')->with('success', 'Communication deleted successfully');
    }
}
