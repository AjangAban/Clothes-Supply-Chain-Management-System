<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index($userId)
    {
        $authUserId = Auth::id();

        // Fetch messages between current user and selected user
        $messages = Message::where(function($query) use ($authUserId, $userId) {
            $query->where('sender_id', $authUserId)->where('receiver_id', $userId);
        })->orWhere(function($query) use ($authUserId, $userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $authUserId);
        })->orderBy('created_at')->get();

        $otherUser = User::findOrFail($userId);

        return view('chat.index', compact('messages', 'otherUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json(['status' => 'Message sent']);
    }

    public function fetch($userId)
    {
        $authUserId = Auth::id();

        $messages = Message::where(function($query) use ($authUserId, $userId) {
            $query->where('sender_id', $authUserId)->where('receiver_id', $userId);
        })->orWhere(function($query) use ($authUserId, $userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $authUserId);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }
}
