<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceArchiveController extends Controller
{
    public function index()
    {
        $invoices = Invoice::onlyTrashed()->get();
        return view('invoices.archives_invoices',compact('invoices'));  
    }

    public function update(Request $request)
    {
        Invoice::withTrashed()->where('id', $request->invoice_id)->restore();
        session()->flash('restore');
        return redirect('/invoices');
    }

    public function destroy(Request $request)
    {
        Invoice::withTrashed()->where('id',$request->invoice_id)->forceDelete();
        session()->flash('delete','تم حذف الفاتورة بنجاح');
        return redirect('/archives');
    }
}
