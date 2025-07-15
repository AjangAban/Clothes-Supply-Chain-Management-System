@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Chat with 
        @foreach($conversation->participants as $participant)
            @if($participant->id !== auth()->id())
                {{ $participant->name }}
            @endif
        @endforeach
    </h4>

    <div id="chat-box" class="border rounded p-3 mb-3" style="height: 300px; overflow-y: scroll;">
        @foreach($messages as $message)
            <div class="mb-2">
                <strong>{{ $message->user->name }}:</strong> {{ $message->body }}
                <br><small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('chat.send', $conversation->id) }}">
        @csrf
        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
            <button class="btn btn-primary" type="submit">Send</button>
        </div>
    </form>
</div>
@endsection


