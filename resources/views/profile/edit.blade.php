<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="h3 mb-1">Perfil - {{$user->name}} - {{ $user->email }}</h3>
            <a href="{{ route('dashboard') }}" class="btn btn-warning shadow-sm">
                <i class="bi bi-x-lg"></i> Cerrar
            </a>
        </div>
    </x-slot>
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-danger shadow-sm">
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
