<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $threads = collect(); // Placeholder – implement messaging

        return view('buyer.messages.index', compact('threads'));
    }

    public function show($id)
    {
        return view('buyer.messages.show', ['threadId' => $id]);
    }
}
