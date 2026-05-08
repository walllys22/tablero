@extends('layouts.app')

@section('title', 'Organizaciones')

@section('content')
    <div class="container-fluid py-4 eventos-browse">
        @if (session('status'))
            <div class="alert alert-success js-auto-dismiss">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                Revise los datos del formulario.
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-8 px-3 py-3">
                                <h1 class="h3 mb-0 text-dark">
                                    <i class="fa-solid fa-torii-gate"></i> Organizaciones
                                </h1>
                            </div>
                            <div class="col-md-4 text-end px-3 py-3">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-create">
                                    <i class="bi bi-plus-lg"></i> <span>Crear</span>
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">
                                    <i class="bi bi-x-lg"></i> <span>Cerrar</span>
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
            <form method="POST" action="{{ route('organizaciones.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateLabel">Crear organizacion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Organizacion</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control @if(!old('editing_organizacion')) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (!old('editing_organizacion'))
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" name="logo" id="logo" class="form-control @if(!old('editing_organizacion')) @error('logo') is-invalid @enderror @endif" accept="image/jpeg,image/png,image/webp" onchange="previewOrganizacionLogo(this, 'logo_preview_create')">
                                @if (!old('editing_organizacion'))
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-2 text-center">
                                <img id="logo_preview_create" src="{{ asset('images/icono.png') }}" alt="Logo organizacion" style="width: 72px; height: 72px; object-fit: contain; border: 1px solid #ced4da; background: #f8f9fa;">
                            </div>

                            <div class="col-md-4">
                                <label for="persona_id" class="form-label">Persona</label>
                                <select name="persona_id" id="persona_id" class="form-select @if(!old('editing_organizacion')) @error('persona_id') is-invalid @enderror @endif" required>
                                    <option value="">Seleccione</option>
                                    @foreach ($personas as $persona)
                                        <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                            {{ $persona->first_name }}{{ $persona->ci ? ' - CI ' . $persona->ci : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (!old('editing_organizacion'))
                                    @error('persona_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
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

    <div class="modal fade" id="modal-delete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="delete_form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeleteLabel">Eliminar organizacion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        Seguro que desea eliminar esta organizacion?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
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

            let url = '{{ route("organizaciones.ajax.list") }}';
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

        function deleteItem(url) {
            $('#delete_form').attr('action', url);
        }

        function previewOrganizacionLogo(input, previewId) {
            let file = input.files && input.files[0];
            let preview = document.getElementById(previewId);

            if (!file || !preview) {
                return;
            }

            preview.src = URL.createObjectURL(file);
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                @if (!old('editing_organizacion'))
                    let modal = new bootstrap.Modal(document.getElementById('modal-create'));
                    modal.show();
                @endif
            });
        @endif
    </script>
@endpush
