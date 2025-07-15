<div class="card">
    <div class="card-body" style="height: 400px; overflow-y: scroll;">
        @foreach($conversation->messages as $message)
            <div class="d-flex {{ $message->user_id == auth()->id() ? 'justify-content-end' : '' }}">
                <div class="card mb-2" style="width: 75%; position: relative;">
                    <div class="card-body">
                        <p class="card-text mb-1" id="message-text-{{ $message->id }}">
                            @if(isset($editingMessageId) && $editingMessageId == $message->id)
                                <form action="{{ route('chat.message.update', [$conversation, $message]) }}" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="body" class="form-control me-2" value="{{ old('body', $message->body) }}" required autofocus>
                                    <button type="submit" class="btn btn-success btn-sm me-1">Resend</button>
                                    <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">Cancel</a>
                                </form>
                            @else
                                {{ $message->body }}
                            @endif
                        </p>
                        <small class="text-muted">{{ $message->created_at->diffForHumans() }} by {{ $message->user->name }}</small>
                        @if($message->user_id == auth()->id())
                            <div class="dropdown position-absolute top-0 end-0 m-2">
                                <button class="btn btn-link text-dark p-0" type="button" id="dropdownMenuButton-{{ $message->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $message->id }}">
                                    <li>
                                        <a class="dropdown-item" href="?edit={{ $message->id }}#message-text-{{ $message->id }}">Edit</a>
                                    </li>
                                    <li>
                                        <form action="{{ route('chat.message.destroy', [$conversation, $message]) }}" method="POST" onsubmit="return confirm('Delete this message?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="card-footer">
        <form action="{{ route('chat.store', $conversation) }}" method="POST" class="d-flex align-items-center">
            @csrf
            <input type="text" name="body" class="form-control me-2" placeholder="Type your message..." required @if(isset($editingMessageId)) disabled @endif>
            <button type="submit" class="btn btn-primary" @if(isset($editingMessageId)) disabled @endif>Send</button>
        </form>
    </div>
</div> 