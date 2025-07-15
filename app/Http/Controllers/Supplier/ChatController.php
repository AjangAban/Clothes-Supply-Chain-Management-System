<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index($partnerId)
    {
        $partner = User::findOrFail($partnerId);
        $user = auth()->user();

        $messages = Message::where(function ($query) use ($user, $partner) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $partner->id);
        })->orWhere(function ($query) use ($user, $partner) {
            $query->where('sender_id', $partner->id)
                  ->where('receiver_id', $user->id);
        })->orderBy('created_at')->get();

        return view('supplier.chat.index', compact('messages', 'partner'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return back();
    }

    public function fetch($partnerId)
{
    $user = auth()->user();
    $partner = \App\Models\User::findOrFail($partnerId);

    $messages = \App\Models\Message::where(function ($query) use ($user, $partner) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $partner->id);
        })
        ->orWhere(function ($query) use ($user, $partner) {
            $query->where('sender_id', $partner->id)
                  ->where('receiver_id', $user->id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json(['messages' => $messages]);
}


    
}
