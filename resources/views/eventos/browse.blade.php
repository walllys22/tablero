@extends('layouts.app')

@section('title', 'Torneos')

@section('content')
    <div class="container-fluid eventos-browse">
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

        <div class="row mb-2" style="margin-right: -25px;">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-8 px-3 py-3">
                                <h1 class="h3 mb-0 text-dark">
                                    <i class="bi bi-trophy"></i> Torneos
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
                                <div class="dataTables_length" id="dataTable_length">
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
                            </div>
                            <div class="col-sm-3 mt-2 mt-sm-0">
                                <input type="text" id="input-search" placeholder="Buscar..." class="form-control">
                            </div>
                        </div>
                        <div class="row" id="div-results" style="min-height: 120px; margin-right: -25px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-create" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form method="POST" action="{{ route('torneos.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateLabel">Crear torneo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" maxlength="255" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" id="ciudad" value="{{ old('ciudad') }}" class="form-control @error('ciudad') is-invalid @enderror" maxlength="255">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="organiza" class="form-label">Organiza</label>
                                <input type="text" name="organiza" id="organiza" value="{{ old('organiza') }}" class="form-control @error('organiza') is-invalid @enderror" maxlength="255">
                                @error('organiza')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-4">
                                <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" min="{{ now()->format('Y-m-d') }}" class="form-control @error('fecha_inicio') is-invalid @enderror">
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_fin" class="form-label">Fecha fin</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" min="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" class="form-control @error('fecha_fin') is-invalid @enderror">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="sistema_competencia" class="form-label">Sistema competencia</label>
                                <select name="sistema_competencia" id="sistema_competencia" class="form-select @error('sistema_competencia') is-invalid @enderror" required>
                                    <option value="tradicional" {{ old('sistema_competencia', 'tradicional') === 'tradicional' ? 'selected' : '' }}>Tradicional</option>
                                    <option value="wkf" {{ old('sistema_competencia') === 'wkf' ? 'selected' : '' }}>WKF</option>
                                    <option value="otro" {{ old('sistema_competencia') === 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('sistema_competencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <div class="col-md-8">
                                <label for="direccion" class="form-label">Direccion</label>
                                <input type="text" name="direccion" id="direccion" value="{{ old('direccion') }}" class="form-control @error('direccion') is-invalid @enderror" maxlength="1000">
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="modalidad_puntaje" class="form-label">Modalidad de puntaje</label>
                                <input type="text" name="modalidad_puntaje" id="modalidad_puntaje" value="{{ old('modalidad_puntaje') }}" class="form-control @error('modalidad_puntaje') is-invalid @enderror" maxlength="100">
                                @error('modalidad_puntaje')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>






                            <div class="col-md-6">
                                <label for="persona_id" class="form-label">Responsable</label>
                                <select name="persona_id" id="persona_id" class="form-select @error('persona_id') is-invalid @enderror" required>
                                    <option value="">Seleccione</option>
                                    @foreach ($personas as $persona)
                                        <option value="{{ $persona->id }}" data-phone="{{ $persona->phone }}" data-email="{{ $persona->email }}" data-address="{{ $persona->address }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                            {{ $persona->first_name }}{{ $persona->ci ? ' - CI ' . $persona->ci : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('persona_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contacto</label>
                                <div class="text-center px-3 py-2" style="min-height: 110px; border: 1px solid #dee2e6; border-radius: 6px; background: #f8f9fa;">
                                    <div id="responsable-phone" class="fw-bold" style="font-size: 13px;">No registrado</div>

                                    <div id="responsable-email" class="mt-1" style="font-size: 13px;">No registrado</div>
                                    <div id="responsable-address" class="text-muted mt-1" style="font-size: 13px;">No registrada</div>
                                </div>
                            </div>





                            <div class="col-md-8">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
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
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="modalDeleteLabel">Eliminar torneo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        Seguro que desea eliminar este torneo?
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

            $('#fecha_inicio').on('change', function () {
                $('#fecha_fin').attr('min', this.value || '{{ now()->format('Y-m-d') }}');
            });

            $('#persona_id').on('change', updateResponsableContacto);
            updateResponsableContacto();
        });

        function list(page = 1) {
            $('#div-results').html('<div class="col-12 text-center text-muted py-5">Cargando...</div>');

            let url = '{{ route("torneos.ajax.list") }}';
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

        function previewTorneoLogo(input, previewId) {
            let file = input.files && input.files[0];
            let preview = document.getElementById(previewId);

            if (!file || !preview) {
                return;
            }

            preview.src = URL.createObjectURL(file);
        }

        function updateResponsableContacto() {
            let selected = $('#persona_id option:selected');
            let phone = selected.data('phone') || '';
            let email = selected.data('email') || '';
            let address = selected.data('address') || '';
            let whatsappPhone = String(phone).replace(/\D+/g, '');

            $('#responsable-phone').text(phone || 'No registrado');
            $('#responsable-email').text(email || 'No registrado');
            $('#responsable-address').text(address || 'No registrada');

            if (whatsappPhone) {
                $('#responsable-whatsapp')
                    .removeClass('d-none')
                    .attr('href', `https://wa.me/${whatsappPhone}?text=Hola`);
            } else {
                $('#responsable-whatsapp')
                    .addClass('d-none')
                    .attr('href', '#');
            }
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                @if (!old('editing_torneo'))
                    let modal = new bootstrap.Modal(document.getElementById('modal-create'));
                    modal.show();
                @endif
            });
        @endif
    </script>
@endpush
