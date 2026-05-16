@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="container-fluid py-4 eventos-browse">
        @if (session('status'))
            <div class="alert alert-success js-auto-dismiss">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">Revise los datos del formulario.</div>
        @endif

        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-8 px-3 py-3">
                                <h1 class="h3 mb-0 text-dark">
                                    <i class="fa-solid fa-users"></i> Usuarios
                                </h1>
                            </div>
                            <div class="col-md-4 text-end px-3 py-3">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-create">
                                    <i class="bi bi-plus-lg"></i> Crear
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">
                                    <i class="bi bi-x-lg"></i> Cerrar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-9">
                                <label class="d-flex align-items-center gap-2 mb-0">
                                    Mostrar
                                    <select id="select-paginate" class="form-select form-select-sm w-auto">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    registros
                                </label>
                            </div>
                            <div class="col-sm-3 mt-2 mt-sm-0">
                                <input type="text" id="input-search" placeholder="Buscar..." class="form-control">
                            </div>
                        </div>

                        <div class="row" id="div-results" style="min-height: 120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-create" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('usuarios.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateLabel">Crear usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @if(!old('editing_usuario')) @error('name') is-invalid @enderror @endif" maxlength="255" required>
                                @if (!old('editing_usuario')) @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @if(!old('editing_usuario')) @error('email') is-invalid @enderror @endif" maxlength="255" required>
                                @if (!old('editing_usuario')) @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-12">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" name="imagen" id="imagen" class="form-control @if(!old('editing_usuario')) @error('imagen') is-invalid @enderror @endif" accept="image/jpeg,image/png,image/webp">
                                @if (!old('editing_usuario')) @error('imagen')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Contrasena</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control @if(!old('editing_usuario')) @error('password') is-invalid @enderror @endif" autocomplete="new-password" required>
                                    <button type="button" class="btn btn-outline-secondary js-toggle-password" data-target="password" aria-label="Ver contrasena">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @if (!old('editing_usuario')) @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmar contrasena</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" required>
                                    <button type="button" class="btn btn-outline-secondary js-toggle-password" data-target="password_confirmation" aria-label="Ver contrasena">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Roles</label>
                                <div class="row g-2">
                                    @foreach ($roles as $role)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="roles[]" id="role_create_{{ $role->id }}" value="{{ $role->id }}" class="form-check-input" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                                <label for="role_create_{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if (!old('editing_usuario')) @error('roles')<div class="text-danger small">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="status" id="status" value="1" class="form-check-input" {{ old('status', 1) ? 'checked' : '' }}>
                                    <label for="status" class="form-check-label">Activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var countPage = 10;
        var timeout = null;

        $(document).ready(function () {
            list();

            $('#input-search').on('keyup', function (event) {
                if (event.keyCode === 13) {
                    clearTimeout(timeout);
                    list();
                }
            });

            $('#select-paginate').change(function () {
                countPage = $(this).val();
                list();
            });

            $('#input-search').on('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    list();
                }, 600);
            });
        });

        function list(page = 1) {
            $('#div-results').html('<div class="col-12 text-center text-muted py-5">Cargando...</div>');

            let url = '{{ route("usuarios.ajax.list") }}';
            let search = $('#input-search').val() ? $('#input-search').val() : '';

            $.ajax({
                url: `${url}?search=${encodeURIComponent(search)}&paginate=${countPage}&page=${page}&_=${Date.now()}`,
                type: 'get',
                cache: false,
                success: function (result) {
                    $('#div-results').html(result);
                },
                error: function () {
                    $('#div-results').html('<div class="col-12"><div class="alert alert-danger mb-0">No se pudo cargar la lista.</div></div>');
                }
            });
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                @if (!old('editing_usuario'))
                    new bootstrap.Modal(document.getElementById('modal-create')).show();
                @endif
            });
        @endif

        document.addEventListener('click', function (event) {
            let button = event.target.closest('.js-toggle-password');
            if (!button) {
                return;
            }

            let input = document.getElementById(button.dataset.target);
            let icon = button.querySelector('i');
            if (!input || !icon) {
                return;
            }

            let showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !showPassword);
            icon.classList.toggle('bi-eye-slash', showPassword);
        });
    </script>
@endpush
