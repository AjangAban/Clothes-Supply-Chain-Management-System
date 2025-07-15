@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Message</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('chat.message.update', [$conversation, $message]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="body" class="form-label">Message</label>
                    <input type="text" name="body" id="body" class="form-control" value="{{ old('body', $message->body) }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('chat.show', $conversation) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection 