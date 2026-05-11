<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="text-align: center">Organizacion</th>
                    <th style="text-align: center">Responsable</th>
                    <th style="text-align: center">Inscripciones</th>
                    <th style="text-align: center">Estado</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $logoUrl = $item->logo
                            ? asset('storage/' . ltrim($item->logo, '/'))
                            : asset('images/icono.png');
                        $logoVersion = optional($item->updated_at)->timestamp ?? $item->id;
                        $logoPreviewUrl = $item->logo ? $logoUrl . '?v=' . $logoVersion : $logoUrl;
                    @endphp
                    <tr>
                        <td style="vertical-align: middle;">
                            <div class="eventos-name-cell">
                                <img src="{{ $logoPreviewUrl }}" alt="{{ $item->nombre ?: 'Organizacion' }}"
                                    class="image-expandable eventos-logo"
                                    style="object-fit: contain; background: #f8f9fa;"
                                    onerror="this.src='{{ asset('images/icono.png') }}'">
                                <div>
                                    <strong>{{ $item->nombre }}</strong><br>
                                    <small class="text-muted">Estilo:
                                        {{ $item->estilo->nombre ?? 'No asignado' }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            @if ($item->persona)
                                <strong>{{ $item->persona->first_name }}</strong><br>
                                <small class="text-muted">
                                    <i class="bi bi-telephone-fill"></i> {{ $item->persona->phone ? 'Telefono: ' . $item->persona->phone : 'Sin telefono' }}
                                </small>
                            @else
                                <span class="text-muted">Sin persona</span>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <label class="label label-info">{{ $item->inscripciones_count }}</label>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->status == 1)
                                <label class="label label-success">Activo</label>
                            @else
                                <label class="label label-danger">Inactivo</label>
                            @endif
                        </td>
                        <td style="vertical-align: middle; width: 14%" class="text-end">
                            @if ($item->status == 1)
                                <button type="button" title="Inactivar" data-bs-toggle="modal"
                                    data-bs-target="#modal-status-{{ $item->id }}"
                                    class="btn btn-sm btn-warning text-white p-1">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('organizaciones.toggle-status', $item) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Activar" class="btn btn-sm btn-warning text-white p-1">
                                        <i class="bi bi-toggle-off"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" title="Ver" data-bs-toggle="modal"
                                data-bs-target="#modal-view-{{ $item->id }}" class="btn btn-sm btn-primary p-1">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" title="Editar" data-bs-toggle="modal"
                                data-bs-target="#modal-edit-{{ $item->id }}"
                                class="btn btn-sm btn-info text-white p-1">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="#"
                                onclick="event.preventDefault(); deleteItem('{{ route('organizaciones.destroy', $item) }}')"
                                title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete"
                                class="btn btn-sm btn-danger p-1">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <h5 class="text-center eventos-empty">
                                <img src="{{ asset('images/empty.png') }}" width="120" alt="Sin resultados">
                                <br><br>
                                No hay resultados
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="row align-items-center">
        <div class="col-md-4" style="overflow-x:auto">
            @if (count($data) > 0)
                <p class="text-muted mb-md-0">Mostrando del {{ $data->firstItem() }} al {{ $data->lastItem() }} de
                    {{ $data->total() }} registros.</p>
            @endif
        </div>
        <div class="col-md-8" style="overflow-x:auto">
            <nav class="d-flex justify-content-md-end">
                {{ $data->links() }}
            </nav>
        </div>
    </div>
</div>

@foreach ($data as $item)
    @php
        $logoUrl = $item->logo ? asset('storage/' . ltrim($item->logo, '/')) : asset('images/icono.png');
        $logoVersion = optional($item->updated_at)->timestamp ?? $item->id;
        $logoPreviewUrl = $item->logo ? $logoUrl . '?v=' . $logoVersion : $logoUrl;
    @endphp

    @if ($item->status == 1)
        <div class="modal fade" id="modal-status-{{ $item->id }}" tabindex="-1"
            aria-labelledby="modalStatusLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form method="POST" action="{{ route('organizaciones.toggle-status', $item) }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark fw-bold">
                            <h5 class="modal-title" id="modalStatusLabel{{ $item->id }}">Alerta</h5>
                            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center">
                            Esta seguro de desactivar la organizacion?
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="submit" class="btn btn-danger">Si</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="modal fade" id="modal-view-{{ $item->id }}" tabindex="-1"
        aria-labelledby="modalViewLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header py-2" style="background: #3a19f5; border-bottom: 0;">
                    <h5 class="modal-title fw-bold text-white" id="modalViewLabel{{ $item->id }}"
                        style="font-size: 20px; color: white;">
                        Datos de Organizacion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-2" style="background: #eeeeee;">
                    <div class="d-flex gap-2 align-items-stretch flex-column flex-md-row">
                        <div class="flex-shrink-0 text-center px-3 py-2"
                            style="width: 150px; min-height: 150px; border-radius: 18px; background: #f8f8f8;">
                            <img src="{{ $logoPreviewUrl }}" alt="{{ $item->nombre ?: 'Logo organizacion' }}"
                                style="width: 112px; height: 112px; object-fit: contain; border: 1px solid #333; background: #ffffff;"
                                onerror="this.src='{{ asset('images/icono.png') }}'">
                            <div class="mt-2 fw-semibold" style="font-size: 13px;">Logo</div>
                        </div>

                        <div class="flex-grow-1">
                            <div class="row g-2 p-1">
                                <div class="col-md-6">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="line-height: 1;">Organizacion
                                        </div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->nombre }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="line-height: 1;">Responsable</div>
                                        <div class="fw-semibold" style="font-size: 14px;">
                                            {{ $item->persona ? $item->persona->first_name : 'Sin persona' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 p-1">
                                <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                    <div class="fw-bold" style="line-height: 1;">Contacto</div>
                                    <div class="fw-semibold" style="font-size: 14px;">
                                        <i class="bi bi-telephone-fill"></i>
                                        {{ $item->persona ? $item->persona->phone ? 'Telefono: ' . $item->persona->phone: 'Sin Registro' : 'Sin Registro' }}
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <i class="bi bi-envelope-fill"></i>
                                        {{ $item->persona ? $item->persona->email ? 'Email: ' . $item->persona->email : 'Sin Registro' : 'Sin Registro' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-2 p-1">
                                <div class="col-md-9">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="line-height: 1;">Estilo</div>
                                        <div class="fw-semibold" style="font-size: 14px;">
                                            {{ $item->estilo ? $item->estilo->nombre . ($item->estilo->descripcion ? ' - ' . $item->estilo->descripcion : '') : 'Sin estilo' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="line-height: 1;">Estado</div>
                                        <div class="fw-semibold" style="font-size: 14px;">
                                            {{ $item->status == 1 ? 'Activo' : 'Inactivo' }}</div>
                                    </div>
                                </div>                            
                            </div>
                            <div class="row g-2 p-1">
                                <div class="col-md-6">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="line-height: 1;">Inscripciones
                                        </div>
                                        <div class="fw-semibold" style="font-size: 14px;">
                                            {{ $item->inscripciones_count }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="line-height: 1;">Fecha de
                                            registro</div>
                                        <div class="fw-semibold" style="font-size: 14px;">
                                            {{ $item->created_at ? $item->created_at->format('d/m/Y') : 'No registrada' }}
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background: #eeeeee; border-top: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-{{ $item->id }}" tabindex="-1"
        aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form method="POST" action="{{ route('organizaciones.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_organizacion" value="{{ $item->id }}">

                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title fw-bold" id="modalEditLabel{{ $item->id }}">Editar organizacion
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" style="background: #eeeeee;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre_edit_{{ $item->id }}" class="form-label">Organizacion</label>
                                <input type="text" name="nombre" id="nombre_edit_{{ $item->id }}"
                                    value="{{ old('editing_organizacion') == $item->id ? old('nombre') : $item->nombre }}"
                                    class="form-control @if (old('editing_organizacion') == $item->id) @error('nombre') is-invalid @enderror @endif"
                                    maxlength="255" required>
                                @if (old('editing_organizacion') == $item->id)
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="logo_edit_{{ $item->id }}" class="form-label">Logo</label>
                                <input type="file" name="logo" id="logo_edit_{{ $item->id }}"
                                    class="form-control @if (old('editing_organizacion') == $item->id) @error('logo') is-invalid @enderror @endif"
                                    accept="image/jpeg,image/png,image/webp"
                                    onchange="previewOrganizacionLogo(this, 'logo_preview_edit_{{ $item->id }}')">
                                @if (old('editing_organizacion') == $item->id)
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                <img id="logo_preview_edit_{{ $item->id }}" src="{{ $logoPreviewUrl }}"
                                    alt="{{ $item->nombre ?: 'Logo organizacion' }}"
                                    style="width: 72px; height: 72px; object-fit: contain; border: 1px solid #ced4da; background: #f8f9fa;"
                                    onerror="this.src='{{ asset('images/icono.png') }}'">
                            </div>
                            <div class="col-md-5">
                                <label for="estilo_id_edit_{{ $item->id }}" class="form-label">Estilo</label>
                                <select name="estilo_id" id="estilo_id_edit_{{ $item->id }}"
                                    class="form-select @if (old('editing_organizacion') == $item->id) @error('estilo_id') is-invalid @enderror @endif">
                                    <option value="">Seleccione</option>
                                    @foreach ($estilos as $estilo)
                                        <option value="{{ $estilo->id }}"
                                            {{ (old('editing_organizacion') == $item->id ? old('estilo_id') : $item->estilo_id) == $estilo->id ? 'selected' : '' }}>
                                            {{ $estilo->nombre }}{{ $estilo->descripcion ? ' - ' . $estilo->descripcion : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (old('editing_organizacion') == $item->id)
                                    @error('estilo_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-5">
                                <label for="persona_edit_{{ $item->id }}" class="form-label">Responsable</label>
                                <select name="persona_id" id="persona_edit_{{ $item->id }}"
                                    class="form-select @if (old('editing_organizacion') == $item->id) @error('persona_id') is-invalid @enderror @endif"
                                    required>
                                    <option value="">Seleccione</option>
                                    @foreach ($personas as $persona)
                                        <option value="{{ $persona->id }}"
                                            {{ (old('editing_organizacion') == $item->id ? old('persona_id') : $item->persona_id) == $persona->id ? 'selected' : '' }}>
                                            {{ $persona->first_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (old('editing_organizacion') == $item->id)
                                    @error('persona_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="status" id="status_edit_{{ $item->id }}"
                                        value="1" class="form-check-input"
                                        {{ old('editing_organizacion') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
                                    <label for="status_edit_{{ $item->id }}" class="form-check-label">Activo</label>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer py-2" style="background: #eeeeee; border-top: 0;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white">
                            <i class="bi bi-check-lg"></i> Actualizar
                        </button>
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
@endforeach

<script>
    $(document).ready(function() {
        $('.page-link').click(function(event) {
            event.preventDefault();

            let link = $(this).attr('href');
            if (link) {
                let url = new URL(link, window.location.origin);
                list(url.searchParams.get('page') || 1);
            }
        });

        @if ($errors->any() && old('editing_organizacion'))
            let editModal = document.getElementById('modal-edit-{{ old('editing_organizacion') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });

    function previewOrganizacionLogo(input, previewId) {
        let file = input.files && input.files[0];
        let preview = document.getElementById(previewId);

        if (!file || !preview) {
            return;
        }

        preview.src = URL.createObjectURL(file);
    }
</script>
