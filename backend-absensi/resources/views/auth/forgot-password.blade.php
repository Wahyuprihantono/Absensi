<x-guest-layout>
    <div class="container container-login container-transparent animated fadeIn">
        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif
        <h3 class="text-center">Lupa Kata Sandi</h3>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="login-form">
                <x-text-field type="email" name="email" label="Email" />
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Lupa kata
                        sandi</button>
                </div>
                <div class="form-group login-account text-left mt-3">
                    <span class="msg">Sudah memiliki akun ?</span>
                    <a href="{{ route('login') }}" id="show-signup" class="link">Masuk</a>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>