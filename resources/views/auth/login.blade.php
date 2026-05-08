<x-guest-layout>
    <h1 class="h4 mb-4 text-center">Iniciar sesion</h1>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="login-form" autocomplete="off">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" value="" required autofocus autocomplete="off" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <div class="input-group">
                <x-text-input id="password" type="password" name="password" value="" required autocomplete="new-password" />
                <button class="btn btn-outline-secondary" type="button" id="toggle-password" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="form-check mb-4">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3">
            @if (Route::has('password.request'))
                <a class="small" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @endif

            <x-primary-button>{{ __('Log in') }}</x-primary-button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const toggleButton = document.getElementById('toggle-password');

                function clearLoginFields() {
                    if (emailInput) {
                        emailInput.value = '';
                    }

                    if (passwordInput) {
                        passwordInput.value = '';
                        passwordInput.type = 'password';
                    }

                    if (toggleButton) {
                        toggleButton.setAttribute('aria-label', 'Mostrar contraseña');
                        toggleButton.querySelector('i').className = 'bi bi-eye';
                    }
                }

                window.addEventListener('pageshow', function (event) {
                    if (event.persisted) {
                        clearLoginFields();
                    }
                });

                if (!passwordInput || !toggleButton) {
                    return;
                }

                toggleButton.addEventListener('click', function () {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    toggleButton.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
                    toggleButton.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            });
        </script>
    @endpush
</x-guest-layout>
