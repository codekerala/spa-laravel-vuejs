<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        return response()
            ->json([
                'model' => Customer::filterPaginateOrder()
            ]);
    }

    public function create()
    {
        return response()
            ->json([
                'form' => Customer::initialize(),
                'option' => []
            ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'company' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required'
        ]);

        $customer = Customer::create($request->all());

        return response()
            ->json([
                'saved' => true
            ]);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        return response()
            ->json([
                'model' => $customer
            ]);
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return response()
            ->json([
                'form' => $customer,
                'option' => []
            ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'company' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required'
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return response()
            ->json([
                'saved' => true
            ]);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // TODO: delete customer's invoices first

        $customer->delete();

        return response()
            ->json([
                'deleted' => true
            ]);
    }
}
