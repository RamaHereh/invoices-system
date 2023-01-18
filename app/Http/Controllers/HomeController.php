<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $count_all =Invoice::count();
        $count_invoices1 = Invoice::where('Value_Status', 1)->count();
        $count_invoices2 = Invoice::where('Value_Status', 2)->count();
        $count_invoices3 = Invoice::where('Value_Status', 3)->count();
        if($count_all==0){
            $percentage1= 0;
            $percentage2= 0;
            $percentage3= 0;
        }
        else{
            $percentage1 = $count_invoices1/ $count_all*100;
            $percentage2 = $count_invoices2/ $count_all*100;
            $percentage3 = $count_invoices3/ $count_all*100;
        }
     
        
        $chartjs = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 350, 'height' => 200])
            ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    "label" => "الفواتير الغير المدفوعة",
                    'backgroundColor' => ['#ec5858'],
                    'data' => [$percentage2]
                ],
                [
                    "label" => "الفواتير المدفوعة",
                    'backgroundColor' => ['#81b214'],
                    'data' => [$percentage1]
                ],
                [
                    "label" => "الفواتير المدفوعة جزئيا",
                    'backgroundColor' => ['#ff9642'],
                    'data' => [$percentage3]
                ],


            ])
            ->options([]);


        $chartjs_2 = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 340, 'height' => 200])
            ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    'backgroundColor' => ['#ec5858', '#81b214','#ff9642'],
                    'data' => [$percentage2,$percentage1,$percentage3]
                ]
            ])
            ->options([]);

        return view('home', compact('chartjs','chartjs_2'));

        }
        }