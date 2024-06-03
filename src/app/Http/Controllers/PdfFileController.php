<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdfFile;
use App\Models\Client;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Storage;

class PdfFileController extends Controller
{
    public function create()
    {
        $clients = Client::all();
        $documentTypes = DocumentType::all();
        return view('upload', compact('clients', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpeg,png,jpg,doc,docx,xls,xlsx|max:20480',
            'document_type' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'client' => 'required|string|max:255',
            'transaction_amount' => 'required|integer',
            'remarks' => 'required|string|max:1000',
        ]);

        $file = $request->file('file');
        $path = $file->store('public/uploaded_files');

        PdfFile::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'document_type' => $request->document_type,
            'transaction_date' => $request->transaction_date,
            'client' => $request->client,
            'transaction_amount' => $request->transaction_amount,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('files.index');
    }

    public function index(Request $request)
    {
        $query = PdfFile::query();

        // AND検索
        if ($request->filled('search_type') && $request->search_type == 'AND') {
            if ($request->filled('transaction_date_start') && $request->filled('transaction_date_end')) {
                $query->whereBetween('transaction_date', [$request->transaction_date_start, $request->transaction_date_end]);
            }
            if ($request->filled('client')) {
                $query->where('client', 'LIKE', '%' . $request->client . '%');
            }
            if ($request->filled('transaction_amount_min')) {
                $query->where('transaction_amount', '>=', $request->transaction_amount_min);
            }
            if ($request->filled('transaction_amount_max')) {
                $query->where('transaction_amount', '<=', $request->transaction_amount_max);
            }
        }

        // OR検索
        if ($request->filled('search_type') && $request->search_type == 'OR') {
            $query->where(function ($q) use ($request) {
                if ($request->filled('transaction_date_start') && $request->filled('transaction_date_end')) {
                    $q->orWhereBetween('transaction_date', [$request->transaction_date_start, $request->transaction_date_end]);
                }
                if ($request->filled('client')) {
                    $q->orWhere('client', 'LIKE', '%' . $request->client . '%');
                }
                if ($request->filled('transaction_amount_min')) {
                    $q->orWhere('transaction_amount', '>=', $request->transaction_amount_min);
                }
                if ($request->filled('transaction_amount_max')) {
                    $q->orWhere('transaction_amount', '<=', $request->transaction_amount_max);
                }
            });
        }

        $files = $query->get();

        return view('files', compact('files'));
    }
}
