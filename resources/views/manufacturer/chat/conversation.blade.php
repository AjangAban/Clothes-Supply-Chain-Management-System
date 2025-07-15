@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $conversation->other_user->name }}</h1>
    @php $editingMessageId = request('edit'); @endphp
    @include('chat._chat-partial', ['conversation' => $conversation, 'editingMessageId' => $editingMessageId])
</div>
@endsection 