<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Invoice;

class Section extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function products(){
        return $this->hasMany(Product::class);
    }
    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

}
