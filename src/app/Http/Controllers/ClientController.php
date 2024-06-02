<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function create()
    {
        return view('clients.create');
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
}
