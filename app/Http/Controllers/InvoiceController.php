<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Section;
use App\Models\InvoiceDetail;
use App\Models\InvoiceAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AddInvoice;
use App\Models\User;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();
        return view('invoices.invoices',compact('invoices'));
    }

    public function create()
    {
        $sections = Section::all();
        return view('invoices.add_invoices', compact('sections'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'invoice_number' => 'required',
            'due_date' => 'required' ,
            'product' => 'required',
            'section_id' => 'required' ,
            'amount_collection' => 'required',
            'amount_commission' => 'required' ,
            'discount' => 'required',
            'rate_vat' => 'required'
        ],[
            'invoice_number.required' =>'يرجى إدخال رقم الفاتورة المنتج',
            'due_date.required' =>'يرجى إدخال تاريخ الاستحقاق',
            'product.required' =>'يرجى إدخال اسم المنتج',
            'section_id.required' =>'يرجى إدخال اسم القسم',
            'amount_collection.required' =>'يرجى إدخال مبلغ التحصيل',
            'amount_commission.required' =>'يرجى إدخال مبلغ العمولة',
            'discount.required' =>'يرجى إدخال نسبة الضريبة',
            'rate_vat.required' =>'يرجى إدخال اسم القسم',
        ]);
        Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->section,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = Invoice::latest()->first()->id;
        InvoiceDetail::create([
            'invoice_id' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'section' => $request->section,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = Invoice::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            

            InvoiceAttachment::create([
                'file_name' => $file_name,
                'invoice_number' => $request->invoice_number,
                'created_by' => Auth::user()->name,
                'invoice_id' => $invoice_id
     

            ]);
              // move pic
              $invoice_number=$request->invoice_number;
              $imageName = $request->pic->getClientOriginalName();
              $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        } 

            $users = User::get();
            $invoice = Invoice::latest()->first();
           Notification::send($users, new \App\Notifications\AddInvoiceNew($invoice));

        // $user = User::get();
        // $invoice = Invoice::latest()->first();
        // Notification::send($user, new \App\Notifications\AddInvoice($invoice));

        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return redirect('/invoices');


    }

    public function show($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        return view('invoices.status_update', compact('invoice'));
    }

    public function edit($id)
    {
       $invoice = Invoice::where('id',$id)->first();
       $sections = Section::get();
       return view ('invoices.edit_invoice',compact('invoice','sections'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->section,
            'amount_collection' => $request->amount_collection,
            'amount_Commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return redirect('/invoices');
    }
    public function destroy(Request $request)
    {
        
        $id_page =$request->id_page;
        $Details = InvoiceAttachment::where('invoice_id', $request->id)->first();
        if ($id_page==1)  {
            if (!empty($Details->invoice_number)) {

              Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
            }
    
            Invoice::where('id', $request->invoice_id)->forceDelete();
            session()->flash('delete','تم حذف الفاتورة بنجاح');
            return redirect('/invoices');
        }
        elseif($id_page==2){
            Invoice::findorFail($request->invoice_id)->delete();
            session()->flash('archive_invoice');
            return redirect('/archives'); 
        }

    }
    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

    public function statusUpdate($id, Request $request)
    {
        $invoice = Invoice::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $invoice->update([
                'value_status' => 1,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);

            InvoiceDetail::create([
                'invoice_id' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 1,
                'note' => $request->note,
                'payment_date' => $request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        }

        else {
            $invoice->update([
                'value_status' => 3,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);
            InvoiceDetail::create([
                'invoice_id' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 3,
                'note' => $request->note,
                'payment_date' => $request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update','تم تحديث حالة الدفع بنجاح');
        return redirect('/invoices');

    }

    public function invoicesPaid()
    {
        $invoices = Invoice::where('value_status', 1)->get();
        return view('invoices.paid_invoices',compact('invoices'));
    }

    public function invoicesUnpaid()
    {
        $invoices = Invoice::where('value_status',2)->get();
        return view('invoices.unpaid_invoices',compact('invoices'));
    }

    public function invoicesPartiallypaid()
    {
        $invoices = Invoice::where('value_status',3)->get();
        return view('invoices.partially_paid',compact('invoices'));
    }
    public function printInvoice($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        return view('invoices.print_invoice',compact('invoice'));
    }

    public function MarkAsRead_all (Request $request)
    {

        $userUnreadNotification= auth()->user()->unreadNotifications;

        if($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
    }
}
