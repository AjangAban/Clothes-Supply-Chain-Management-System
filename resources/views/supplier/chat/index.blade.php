@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Chat with Manufacturer</h4>

    <div class="card">
        <div class="card-body" style="height: 400px; overflow-y: scroll;" id="chat-box">
            @foreach ($messages as $message)
                <div class="mb-2">
                    <strong>{{ $message->sender->name }}:</strong> {{ $message->message }}
                    <br>
                    <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                </div>
            @endforeach
        </div>
    </div>

    <form id="chat-form" method="POST" action="{{ route('supplier.chat.send') }}" class="mt-3">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $partner->id }}">
        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const chatBox = document.getElementById('chat-box');

    setInterval(() => {
        fetch("{{ route('supplier.chat.fetch', ['partner' => $partner->id]) }}")
            .then(response => response.json())
            .then(data => {
                let html = '';
                data.messages.forEach(message => {
    const isOwn = message.sender_id === {{ auth()->id() }};
    const senderLabel = isOwn ? 'You' : "{{ $partner->name }}";

    html += `
        <div class="mb-2 text-${isOwn ? 'end' : 'start'}">
            <span class="badge bg-${isOwn ? 'primary' : 'secondary'}">
                ${message.message}
            </span>
            <div class="text-muted small">
                ${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}
            </div>
        </div>
    `;
});

                chatBox.innerHTML = html;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }, 5000);
</script>
@endsection
