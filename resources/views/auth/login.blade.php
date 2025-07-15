@extends('layouts.app')
@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <img src="/light-bootstrap/img/new_logo.png" alt="Logo" style="height: 50px; margin-bottom: 10px;">
                <div>Login to GenZ FashionZ Supply Chain</div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                        </div>
                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Login as</label>
                        <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->display_name ?? $role->name) }}</option>
                            @endforeach
                        </select>
                        @error('role_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Enter your password">
                            <span class="input-group-text" style="cursor:pointer" onclick="togglePasswordVisibility()"><i class="fas fa-eye" id="togglePasswordIcon"></i></span>
                        </div>
                        @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <div class="text-center mt-4">
                        <span>Don't have an account?</span>
                        <a href="{{ route('register') }}" class="fw-bold text-success ms-1" style="text-decoration: underline;">Sign up</a>
                    </div>
                    <!--
                    <div class="text-center mt-3">
                        <span>Or login with</span>
                        <div class="mt-2">
                            <button class="btn btn-outline-danger btn-sm mx-1"><i class="fab fa-google"></i></button>
                            <button class="btn btn-outline-primary btn-sm mx-1"><i class="fab fa-facebook-f"></i></button>
                        </div>
                    </div>
                    -->
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection