<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdfFile;
use App\Models\Client;
use App\Models\DocumentType;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class PdfFileController extends Controller
{
    public function create()
    {
        $clients = Client::all();
        $documentTypes = DocumentType::all();
        $files = PdfFile::paginate(10); // ページネーションを使用

        return view('upload', compact('files', 'clients', 'documentTypes'));
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

        // フラッシュメッセージを設定
        $request->session()->flash('success', '登録完了しました');

        return redirect()->route('upload.form');
    }

    public function index(Request $request)
    {
        $query = PdfFile::query();

        if ($request->filled('transaction_date_start')) {
            $query->where('transaction_date', '>=', $request->transaction_date_start);
        }

        if ($request->filled('transaction_date_end')) {
            $query->where('transaction_date', '<=', $request->transaction_date_end);
        }

        if ($request->filled('client')) {
            $query->where('client', 'like', '%' . $request->client . '%');
        }

        if ($request->filled('transaction_amount_min')) {
            $query->where('transaction_amount', '>=', $request->transaction_amount_min);
        }

        if ($request->filled('transaction_amount_max')) {
            $query->where('transaction_amount', '<=', $request->transaction_amount_max);
        }

        if ($request->filled('search_type') && $request->search_type == 'OR') {
            // Implement OR search logic if required.
        }

        $files = $query->paginate(10); // Paginate the results

        return view('files', compact('files'));
    }

    public function edit($id)
    {
        $file = PdfFile::findOrFail($id);
        $clients = Client::all();
        $documentTypes = DocumentType::all();
        return view('edit', compact('file', 'clients', 'documentTypes'));
    }

    public function destroy($id)
    {
        $file = PdfFile::findOrFail($id);
        $file->delete();

        return redirect()->route('files.index')->with('success', 'ファイルが削除されました。');
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
        $file->update($request->all());

        return redirect()->route('files.index')->with('success', 'ファイル情報が更新されました。');
    }
    public function downloadZip(Request $request)
    {
        $query = PdfFile::query();

        if ($request->filled('transaction_date_start')) {
            $query->where('transaction_date', '>=', $request->transaction_date_start);
        }

        if ($request->filled('transaction_date_end')) {
            $query->where('transaction_date', '<=', $request->transaction_date_end);
        }

        if ($request->filled('client')) {
            $query->where('client', 'like', '%' . $request->client . '%');
        }

        if ($request->filled('transaction_amount_min')) {
            $query->where('transaction_amount', '>=', $request->transaction_amount_min);
        }

        if ($request->filled('transaction_amount_max')) {
            $query->where('transaction_amount', '<=', $request->transaction_amount_max);
        }

        $files = $query->get();

        if ($files->isEmpty()) {
            return back()->with('error', '該当するファイルがありません。');
        }

        $zip = new ZipArchive;
        $zipFileName = 'files.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                $filePath = storage_path('app/' . $file->path);
                $relativeNameInZipFile = basename($filePath);
                $zip->addFile($filePath, $relativeNameInZipFile);
            }
            $zip->close();
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
