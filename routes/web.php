<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\InvoiceAttachmentController;
use App\Http\Controllers\InvoiceArchiveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles',RoleController::class);
    Route::resource('users',UserController::class);
    });


//reports
Route::get('reports_invoices', [ReportController::class,'indexInvoices']);
Route::post('search_invoices', [ReportController::class,'searchInvoices']);
Route::get('reports_sections', [ReportController::class,'indexSections']);
Route::post('search_sections', [ReportController::class,'searchSections']);

//noti
Route::get('MarkAsRead_all', [InvoiceController::class,'MarkAsRead_all'])->name('MarkAsRead_all');

//Attachment
Route::post('deleteFile', [InvoiceAttachmentController::class,'deleteFile']);
Route::get('/viewFile/{invoice_number}/{file_name}', [InvoiceAttachmentController::class,'viewFile']);
Route::get('/getFile/{invoice_number}/{file_name}', [InvoiceAttachmentController::class,'getFile']);
Route::resource('/addAttachment', InvoiceAttachmentController::class);

Route::resource('sections', SectionController::class);
Route::resource('archives', InvoiceArchiveController::class);
Route::resource('products', ProductController::class);
Route::post('status/{idpage}', [InvoiceController::class,'statusUpdate'])->name('status');
Route::get('/paid', [InvoiceController::class,'invoicesPaid']);
Route::get('/unpaid', [InvoiceController::class,'invoicesUnpaid']);
Route::get('/partially', [InvoiceController::class,'invoicesPartiallypaid']);
Route::resource('/invoices', InvoiceController::class);
Route::get('/printinvoice{id}', [InvoiceController::class , 'printInvoice'])->name('printinvoice');

Route::get('section/{id}', [InvoiceController::class,'getproducts']);
Route::resource('invoicesDetails', InvoiceDetailController::class);





Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();





Route::resource('/{page}', AdminController::class);









