@extends('layouts.app') {{-- Or your dashboard layout --}}

@section('content')
<div class="container py-4">
    <div class="card shadow rounded">
        <div class="card-header bg-primary text-white">
            Chat with {{ $partner->name }}
        </div>
        <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto; background-color: #f9f9f9;">
            <div id="messages">
                @foreach ($messages as $message)
                    <div class="mb-2 text-{{ $message->sender_id === auth()->id() ? 'end' : 'start' }}">
                        <span class="badge bg-{{ $message->sender_id === auth()->id() ? 'primary' : 'secondary' }}">
                            {{ $message->message }}
                        </span>
                        <div class="text-muted small">
                            {{ $message->created_at->format('H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer">
            <form id="message-form">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $partner->id }}">
                <div class="input-group">
                    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Scroll to bottom initially
    function scrollToBottom() {
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    scrollToBottom();

    // Submit message via AJAX
    $('#message-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const formData = form.serialize();

        $.post('{{ route("manufacturer.chat.send") }}', formData, function(response) {
            form[0].reset();
            fetchMessages(); // refresh messages
        }).fail(function(xhr){
            alert("Failed to send message.");
            console.log(xhr.responseText);
        });
    });

    // Fetch new messages every 3 seconds
    function fetchMessages() {
        $.get('{{ route("manufacturer.chat.fetch", $partner->id) }}', function(response) {
            let html = '';
            response.messages.forEach(function(message) {
                const isOwn = message.sender_id === {{ auth()->id() }};
                html += `
                    <div class="mb-2 text-${isOwn ? 'end' : 'start'}">
                        <span class="badge bg-${isOwn ? 'primary' : 'secondary'}">${message.message}</span>
                        <div class="text-muted small">${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                    </div>
                `;
            });
            $('#messages').html(html);
            scrollToBottom();
        });
    }

    setInterval(fetchMessages, 3000); // Poll every 3 seconds
</script>
@endsection
