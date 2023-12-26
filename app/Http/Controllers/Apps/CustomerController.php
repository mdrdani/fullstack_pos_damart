<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    //

    public function index()
    {
        $customers = Customer::when(request()->q, function ($customers) {
            $customers = $customers->where('name', 'LIKE', '%' . request()->q . '%');
        })->latest()->paginate(10);

        // return inertia
        return inertia('Apps/Customers/Index', [
            'customers' => $customers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Apps/Customers/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'no_telp'   => 'required|unique:customers',
            'address'   => 'required',
        ]);

        //create customer
        Customer::create([
            'name'      => $request->name,
            'no_telp'   => $request->no_telp,
            'address'   => $request->address,
        ]);

        return redirect()->route('apps.customers.index');
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Apps/Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'      => 'required',
            'no_telp'   => 'required|unique:customers,no_telp,' . $customer->id,
            'address'   => 'required',
        ]);

        //update customer
        $customer->update([
            'name'      => $request->name,
            'no_telp'   => $request->no_telp,
            'address'   => $request->address,
        ]);

        return redirect()->route('apps.customers.index');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('apps.customers.index');
    }
}
