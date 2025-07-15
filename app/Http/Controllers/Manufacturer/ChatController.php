<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Show chat view
    public function index($partnerId)
    {
        $partner = User::findOrFail($partnerId);
        $user = Auth::user();

        // Fetch existing messages between manufacturer and supplier
        $messages = Message::where(function ($query) use ($user, $partner) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $partner->id);
            })
            ->orWhere(function ($query) use ($user, $partner) {
                $query->where('sender_id', $partner->id)
                      ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('manufacturer.chat.index', compact('messages', 'partner'));
    }

    // Store a new message
   public function store(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'message' => 'required|string',
    ]);

    \App\Models\Message::create([
        'sender_id' => Auth::id(),
        'receiver_id' => $request->receiver_id,
        'message' => $request->message,
    ]);

    return response()->json(['message' => 'Message sent successfully.']);
}
 

    // Fetch latest messages
    public function fetch($partnerId)
    {
        $user = Auth::user();
        $partner = User::findOrFail($partnerId);

        $messages = Message::where(function ($query) use ($user, $partner) {
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
