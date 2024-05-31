<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdfFile;
use Illuminate\Support\Facades\Storage;

class PdfFileController extends Controller

{
    // アップロードフォームを表示するメソッド
    public function create()
    {
        return view('upload'); // upload.blade.phpビューを表示
    }

    // ファイルを保存するメソッド
    public function store(Request $request)
    {
        // PDFファイルのバリデーション
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:2048',
            'document_type' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'client' => 'required|string|max:255',
            'transaction_amount' => 'required|integer',
            'remarks' => 'required|string|max:1000',
        ]);

        // アップロードされたPDFファイルを取得
        $pdf = $request->file('pdf');
        $path = $pdf->store('pdf_files');

        // PdfFileモデルを使ってデータベースにレコードを作成
        PdfFile::create([
            'name' => $pdf->getClientOriginalName(),
            'path' => $path,
            'document_type' => $request->document_type,
            'transaction_date' => $request->transaction_date,
            'client' => $request->client,
            'transaction_amount' => $request->transaction_amount,
            'remarks' => $request->remarks,
        ]);

        // ファイル一覧ページにリダイレクト
        return redirect()->route('files.index');
    }

    // ファイルの一覧を表示するメソッド
    public function index()
    {
        $files = PdfFile::all(); // すべてのPdfFileレコードを取得
        return view('files', compact('files')); // files.blade.phpビューにデータを渡す
    }
}
