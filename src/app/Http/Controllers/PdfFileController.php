<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdfFile;
use App\Models\Client;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Storage;

class PdfFileController extends Controller

{
    // アップロードフォームを表示するメソッド
    public function create()
    {
        $clients = Client::all(); // すべてのクライアントを取得
        $documentTypes = DocumentType::all(); // すべての証票種別を取得
        return view('upload', compact('clients', 'documentTypes')); // クライアントデータと証票種別データをビューに渡す
    }

    // ファイルを保存するメソッド
    public function store(Request $request)
    {
        // ファイルのバリデーション
        $request->validate([
            'file' => 'required|mimes:pdf,jpeg,png,jpg,doc,docx,xls,xlsx|max:20480', // 20MBまでのファイルを許可
            'document_type' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'client' => 'required|string|max:255',
            'transaction_amount' => 'required|integer',
            'remarks' => 'required|string|max:1000',
        ]);

        // アップロードされたファイルを取得
        $file = $request->file('file');
        $path = $file->store('uploaded_files');

        // PdfFileモデルを使ってデータベースにレコードを作成
        PdfFile::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'file_type' => $file->getClientOriginalExtension(), // ファイルタイプを保存
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
