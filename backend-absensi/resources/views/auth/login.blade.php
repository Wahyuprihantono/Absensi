<x-guest-layout>
    <div class="container container-login container-transparent animated fadeIn">
        @if (session()->has('status'))
        <div class="alert alert-success">{{session()->get('status')}}</div>
        @endif
        @if (session()->has('error'))
        <div class="alert alert-danger">{{session()->get('error')}}</div>
        @endif
        <h3 class="text-center">Masuk</h3>
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="login-form">
                <div class="form-group">
                    <label for="email" class="placeholder"><b>Email</b></label>
                    <input id="email" type="text" class="form-control" name="email" :value="old('email')" required>
                </div>
                <div class="form-group">
                    <label for="password" class="placeholder"><b>Password</b></label>
                    @if (Route::has('password.request'))
                    <a class="link float-right" href="{{ route('password.request') }}">
                        {{ __('Lupa kata sandi?') }}
                    </a>
                    @endif
                    <div class="position-relative">
                        <input id="password" name="password" type="password" class="form-control" required>
                        {{-- <div class="show-password">
                            <i class="icon-eye"></i>
                        </div> --}}
                    </div>
                </div>
                <div class="form-group form-action-d-flex mb-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember" id="rememberme">
                        <label class="custom-control-label m-0" for="rememberme">Remember Me</label>
                    </div>
                    <button type="submit"
                        class="btn btn-secondary col-md-5 float-right mt-3 mt-sm-0 fw-bold">Masuk</button>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>