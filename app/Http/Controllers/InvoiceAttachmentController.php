<?php

namespace App\Http\Controllers;

use App\Models\InvoiceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use File;
use Illuminate\Support\Facades\Response;

class InvoiceAttachmentController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [

            'file_name' => 'mimes:pdf,jpeg,png,jpg',
    
            ], [
                'file_name.mimes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg',
            ]);
            
            $image = $request->file('file_name');
            $file_name = $image->getClientOriginalName();
            

            InvoiceAttachment::create([
                'file_name' => $file_name,
                'invoice_number' => $request->invoice_number,
                'created_by' => Auth::user()->name,
                'invoice_id' => $request->invoice_id
     

            ]);
              // move pic
              $imageName = $request->file_name->getClientOriginalName();
              $request->file_name->move(public_path('Attachments/'. $request->invoice_number), $imageName);
        

              session()->flash('Add', 'تم اضافة المرفق بنجاح');
              return back();
            
    }
    
    public function deleteFile (Request $request)
    {
        $invoices = InvoiceAttachment::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

     public function getFile($invoice_number,$file_name)

    {
        $path= public_path('Attachments/'.$invoice_number.'/'.$file_name);
        return response()->download($path );
    }

    public function viewFile($invoice_number,$file_name)
    {
        $path= public_path('Attachments/'.$invoice_number.'/'.$file_name);
        return response()->file($path);
    }
}
