<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Section;

class Invoice extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $guarded = [];

    public function section()
    {
    return $this->belongsTo(Section::class);
    }
}
