<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'path', 'document_type', 'transaction_date', 'client', 'transaction_amount', 'remarks'
    ];
}
