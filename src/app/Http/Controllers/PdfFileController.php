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

        // 日付検索
        if ($request->filled('transaction_date')) {
            if ($request->search_type == 'OR') {
                $query->orWhere('transaction_date', $request->transaction_date);
            } else {
                $query->where('transaction_date', $request->transaction_date);
            }
        }

        // 取引先検索
        if ($request->filled('client')) {
            if ($request->search_type == 'OR') {
                $query->orWhere('client', 'like', '%' . $request->client . '%');
            } else {
                $query->where('client', 'like', '%' . $request->client . '%');
            }
        }

        // 取引金額検索
        if ($request->filled('transaction_amount_min')) {
            if ($request->search_type == 'OR') {
                $query->orWhere('transaction_amount', '>=', $request->transaction_amount_min);
            } else {
                $query->where('transaction_amount', '>=', $request->transaction_amount_min);
            }
        }

        if ($request->filled('transaction_amount_max')) {
            if ($request->search_type == 'OR') {
                $query->orWhere('transaction_amount', '<=', $request->transaction_amount_max);
            } else {
                $query->where('transaction_amount', '<=', $request->transaction_amount_max);
            }
        }

        $files = $query->get();
        return view('files', compact('files'));
    }

    public function edit($id)
    {
        $file = PdfFile::findOrFail($id);
        $clients = Client::all();
        $documentTypes = DocumentType::all();
        return view('edit', compact('file', 'clients', 'documentTypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'client' => 'required|string|max:255',
            'transaction_amount' => 'required|integer',
            'remarks' => 'required|string|max:1000',
        ]);

        $file = PdfFile::findOrFail($id);
        $file->update([
            'name' => $request->name,
            'document_type' => $request->document_type,
            'transaction_date' => $request->transaction_date,
            'client' => $request->client,
            'transaction_amount' => $request->transaction_amount,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('files.index')->with('success', 'ファイル情報が更新されました。');
    }

    public function destroy($id)
    {
        $file = PdfFile::findOrFail($id);

        // ファイルをストレージから削除
        Storage::delete($file->path);

        // データベースからレコードを削除
        $file->delete();

        return redirect()->route('files.index')->with('success', 'ファイルが削除されました。');
    }
}
