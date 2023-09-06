<x-guest-layout>
    <div class="container container-login container-transparent animated fadeIn">
        <h3 class="text-center">Reset Kata Sandi</h3>
        <livewire:auth.password-reset token="{{request()->segment(2)}}" />
        {{-- @livewire('reset-password', ['token' => ]) --}}
    </div>
</x-guest-layout>