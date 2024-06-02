<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    public function create()
    {
        $documentTypes = DocumentType::all(); // すべての証票種別を取得
        return view('document_types.create', compact('documentTypes')); // 証票種別データをビューに渡す
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DocumentType::create([
            'name' => $request->name,
        ]);

        return redirect()->route('document_types.create')->with('success', '証票種別が追加されました。');
    }

    public function edit($id)
    {
        $documentType = DocumentType::findOrFail($id);
        return view('document_types.edit', compact('documentType'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $documentType = DocumentType::findOrFail($id);
        $documentType->update([
            'name' => $request->name,
        ]);

        return redirect()->route('document_types.create')->with('success', '証票種別が更新されました。');
    }
}
