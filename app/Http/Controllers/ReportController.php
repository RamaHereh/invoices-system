<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Section;

class ReportController extends Controller
{
   public function indexInvoices(){
      return view('reports.invoices_report');       
      }
   
    public function searchInvoices(Request $request){
      $rdio = $request->rdio;
  
      if ($rdio == 1) {

         if ($request->type && $request->start_at =='' && $request->end_at =='') {    
            $invoices = Invoice::where('Status',$request->type)->get();
            $type = $request->type;
            return view('reports.invoices_report',compact('type'))->withDetails($invoices);
         }

         else {
              
            $start_at = date($request->start_at);
            $end_at = date($request->end_at);
            $invoices = Invoice::whereBetween('invoice_date',[$start_at,$end_at])->where('status','=',$request->type)->get();
            $type = $request->type; 
            return view('reports.invoices_report',compact('type','start_at','end_at'))->withDetails($invoices); 
         }
       
      } 
       
      else {
           
         $invoices = Invoice::where('invoice_number',$request->invoice_number)->get();
         return view('reports.invoices_report')->withDetails($invoices);
           
      }
   
       
        
   }

   public function indexSections(){

      $sections = Section::all();
      return view('reports.sections_report',compact('sections'));
          
   }
  
   public function searchSections(Request $request){
        
      if ($request->section && $request->product && $request->start_at =='' && $request->end_at=='') {
  
         $invoices = Invoice::where('section_id',$request->section)->where('product',$request->product)->get();
         $sections = Section::all();
         return view('reports.sections_report',compact('sections'))->withDetails($invoices);
  
      }

      else {
         
         $start_at = date($request->start_at);
         $end_at = date($request->end_at);
         $invoices = Invoice::whereBetween('invoice_date',[$start_at,$end_at])->where('section_id','=',$request->Section)->where('product','=',$request->product)->get();
         $sections = Section::all();
         return view('reports.sections_report',compact('sections','start_at','end_at'))->withDetails($invoices);
    
      }         
   }
}
