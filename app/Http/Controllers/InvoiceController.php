<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\InvoiceItem;
use App\Customer;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()
            ->json([
                'model' => Invoice::with('customer')->filterPaginateOrder()
            ]);
    }

    public function create()
    {
        return response()
            ->json([
                'form' => Invoice::initialize(),
                'option' => [
                    'customers' => Customer::orderBy('name')->get()
                ]
            ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required',
            'date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d',
            'discount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        $data = $request->except('items');
        $data['sub_total'] = 0;
        $items = [];

        foreach($request->items as $item) {
            $data['sub_total'] += $item['unit_price'] * $item['qty'];
            $items[] = new InvoiceItem($item);
        }

        $data['total'] = $data['sub_total'] - $data['discount'];

        $invoice = Invoice::create($data);

        $invoice->items()
            ->saveMany($items);

        return response()
            ->json([
                'saved' => true
            ]);
    }

    public function show($id)
    {
        $invoice = Invoice::with('customer', 'items')->findOrFail($id);

        return response()
            ->json([
                'model' => $invoice
            ]);
    }

    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        return response()
            ->json([
                'form' => $invoice,
                'option' => [
                    'customers' => Customer::orderBy('name')->get()
                ]
            ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required',
            'date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d',
            'discount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        $invoice = Invoice::findOrFail($id);

        $data = $request->except('items');
        $data['sub_total'] = 0;
        $items = [];
        $itemIds = [];

        foreach($request->items as $item) {
            $data['sub_total'] += $item['unit_price'] * $item['qty'];
            if(isset($item['id'])) {
                // update the item
                InvoiceItem::whereId($item['id'])
                    ->whereInvoiceId($invoice->id)
                    ->update($item);
                $itemIds[] = $item['id'];
            } else {
                $items[] = new InvoiceItem($item);
            }
        }

        $data['total'] = $data['sub_total'] - $data['discount'];

        $invoice->update($data);

        // delete removed items

        if(count($itemIds)) {
            InvoiceItem::whereInvoiceId($invoice->id)
                ->whereNotIn('id', $itemIds)
                ->delete();
        }

        if(count($items)) {
            $invoice->items()
                ->saveMany($items);
        }

        return response()
            ->json([
                'saved' => true
            ]);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        InvoiceItem::whereInvoiceId($invoice->id)
            ->delete();

        $invoice->delete();

        return response()
            ->json([
                'deleted' => true
            ]);
    }
}
