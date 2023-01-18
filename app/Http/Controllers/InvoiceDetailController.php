<?php

namespace App\Http\Controllers;

use App\Models\InvoiceDetail;
use App\Models\InvoiceAttachment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceDetailController extends Controller
{
    public function show($id)
    {
        $invoices = Invoice::where('id',$id)->first();
        $details = InvoiceDetail::where('invoice_id',$id)->get();
        $attechments = InvoiceAttachment::where('invoice_id',$id)->get();
        return view('invoices.details_invoice',compact('invoices','details','attechments'));

    }

}
