<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function create()
    {
        $clients = Client::all(); // すべてのクライアントを取得
        return view('clients.create', compact('clients')); // クライアントデータをビューに渡す
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Client::create([
            'name' => $request->name,
        ]);

        return redirect()->route('clients.create')->with('success', '取引先が追加されました。');
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $client = Client::findOrFail($id);
        $client->update([
            'name' => $request->name,
        ]);

        return redirect()->route('clients.create')->with('success', '取引先が更新されました。');
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.create')->with('success', '取引先が削除されました。');
    }
}
