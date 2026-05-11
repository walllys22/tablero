<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover eventos-table w-100">
            <thead>
                <tr>
                    <th style="text-align: center">Torneo</th>
                    <th style="text-align: center">Responsable</th>
                    <th style="text-align: center">Ciudad</th>
                    <th style="text-align: center">Sistema</th>
                    <th style="text-align: center">Fecha</th>
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
                        $responsableWhatsapp = preg_replace('/\D+/', '', $item->persona->phone ?? '');
                    @endphp

                    <tr>
                        <td style="vertical-align: middle;">
                            <div class="eventos-name-cell">
                                @if ($item->logo)
                                    <img src="{{ $logoPreviewUrl }}" alt="{{ $item->nombre ?: 'Torneo' }}"
                                        class="image-expandable eventos-logo"
                                        style="object-fit: contain; background: #f8f9fa;"
                                        onerror="this.src='{{ asset('images/icono.png') }}'">
                                @endif
                                <div>
                                    <strong>{{ $item->nombre ?: 'Sin nombre' }}</strong><br>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->persona)
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <strong>{{ $item->persona->first_name }}</strong>
                                    @if ($item->persona->phone)
                                        <span
                                            style="font-weight: bold; font-size: 13px; white-space: nowrap;">{{ $item->persona->phone }}</span>
                                    @else
                                        <span class="text-muted" style="font-style: italic;">Sin telefono</span>
                                    @endif
                                    @if ($item->persona->email)
                                        <small
                                            style="margin-top: 5px; display: block;">{{ $item->persona->email }}</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">Sin responsable</span>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <strong>{{ $item->ciudad ?: ($item->lugar ?: 'Sin ciudad') }}</strong>
                            @if ($item->direccion)
                                <br><small class="text-muted">{{ $item->direccion }}</small>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <label
                                class="label label-info">{{ strtoupper($item->sistema_competencia ?: 'tradicional') }}</label>
                            @if ($item->modalidad_puntaje)
                                <br><small class="text-muted">{{ $item->modalidad_puntaje }}</small>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->fecha_inicio)
                                <span>
                                    desde el {{ $item->fecha_inicio->format('d/m/Y') }}
                                    al {{ $item->fecha_fin->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">Sin fecha</span>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->status == 1)
                                <label class="label label-success">Activo</label>
                            @else
                                <label class="label label-danger">Inactivo</label>
                            @endif
                        </td>
                        <td style="vertical-align: middle; width: 14%"
                            class="no-sort no-click bread-actions text-end p-2">
                            <div class="d-flex flex-wrap justify-content-end gap-1">
                                <a href="{{ route('modalidades.index', ['torneo' => $item, 'return' => 'torneos']) }}"
                                    title="Modalidades" class="btn btn-sm btn-primary">
                                    <i class="bi bi-list-check"></i>
                                </a>
                                <a href="{{ route('inscripciones.index', $item) }}" title="Inscripciones"
                                    class="btn btn-sm btn-success">
                                    <i class="bi bi-person-check"></i>
                                </a>
                                <a href="{{ route('arbitros.index', $item) }}" title="Jueces"
                                    class="btn btn-sm btn-secondary">
                                    <i class="bi bi-person-badge"></i>
                                </a>
                                <button type="button" title="Costos de inscripcion" data-bs-toggle="modal"
                                    data-bs-target="#modal-costos-{{ $item->id }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-cash-stack"></i>
                                </button>
                                @if ($item->status == 1)
                                    <button type="button" title="Inactivar" data-bs-toggle="modal"
                                        data-bs-target="#modal-status-{{ $item->id }}"
                                        class="btn btn-sm btn-warning text-white">
                                        <i class="bi bi-toggle-on"></i>
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('torneos.toggle-status', $item) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Activar"
                                            class="btn btn-sm btn-warning text-white">
                                            <i class="bi bi-toggle-off"></i>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" title="Ver" data-bs-toggle="modal"
                                    data-bs-target="#modal-view-{{ $item->id }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" title="Editar" data-bs-toggle="modal"
                                    data-bs-target="#modal-edit-{{ $item->id }}"
                                    class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="#"
                                    onclick="event.preventDefault(); deleteItem('{{ route('torneos.destroy', $item) }}')"
                                    title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete"
                                    class="btn btn-sm btn-danger delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
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
                <form method="POST" action="{{ route('torneos.toggle-status', $item) }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark fw-bold">
                            <h5 class="modal-title" id="modalStatusLabel{{ $item->id }}">Alerta</h5>
                            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center">
                            Esta seguro de desactivar el campeonato?
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

    <div class="modal fade" id="modal-costos-{{ $item->id }}" tabindex="-1"
        aria-labelledby="modalCostosLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('torneos.costos.update', $item) }}">
                @csrf
                @method('PATCH')

                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalCostosLabel{{ $item->id }}">
                            <i class="bi bi-cash-stack"></i> Costos de inscripcion
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Torneo</label>
                            <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                {{ $item->nombre ?: 'Torneo sin nombre' }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="costo_organizacion_{{ $item->id }}" class="form-label">Inscripcion de
                                Organizacion</label>
                            <input type="number" name="costo_inscripcion_organizacion"
                                id="costo_organizacion_{{ $item->id }}"
                                value="{{ old('costo_inscripcion_organizacion', $item->costo_inscripcion_organizacion ?? 0) }}"
                                class="form-control" min="0" step="0.01" required>
                        </div>
                        <div>
                            <label for="costo_competidor_{{ $item->id }}" class="form-label">Inscripcion de
                                Competidores</label>
                            <input type="number" name="costo_inscripcion_competidor"
                                id="costo_competidor_{{ $item->id }}"
                                value="{{ old('costo_inscripcion_competidor', $item->costo_inscripcion_competidor ?? 0) }}"
                                class="form-control" min="0" step="0.01" required>
                            <small class="text-muted">Este valor se usara como costo por categoria/modalidad del
                                competidor.</small>
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

    <div class="modal fade" id="modal-view-{{ $item->id }}" tabindex="-1"
        aria-labelledby="modalViewLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 overflow-hidden shadow">
                <div class="modal-header py-2" style="background: #3a19f5; border-bottom: 0;">
                    <h5 class="modal-title fw-bold text-white" id="modalViewLabel{{ $item->id }}"
                        style="font-size: 20px; color: white;">
                        Datos del Torneo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4" style="background: #f2f6ff;">
                    <div class="torneo-view-card">
                        <div class="row g-3 align-items-start">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <img src="{{ $logoPreviewUrl }}" alt="{{ $item->nombre ?: 'Logo torneo' }}"
                                        class="torneo-logo-preview mx-auto mb-3"
                                        onerror="this.src='{{ asset('images/icono.png') }}'">
                                    <div class="fw-semibold" style="font-size: 14px;">Logo Torneo</div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Nombre</div>
                                            <div class="field-value">{{ $item->nombre ?: 'Sin nombre' }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Ciudad</div>
                                            <div class="field-value">
                                                {{ $item->ciudad ?: ($item->lugar ?: 'Sin ciudad') }}</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="torneo-view-field d-flex flex-column">
                                            <div class="field-label">Responsable</div>
                                            <div class="field-value">
                                                {{ $item->persona ? $item->persona->first_name : 'Sin responsable' }}
                                                <br>
                                                {{ $item->persona && $item->persona->phone ? $item->persona->phone : 'Sin telefono' }}  -  
                                                {{ $item->persona && $item->persona->email ? $item->persona->email : 'Sin email' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Fecha inicio</div>
                                            <div class="field-value">
                                                {{ $item->fecha_inicio ? $item->fecha_inicio->format('d/m/Y') : 'Sin fecha' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Fecha fin</div>
                                            <div class="field-value">
                                                {{ $item->fecha_fin ? $item->fecha_fin->format('d/m/Y') : 'Sin fecha' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="torneo-view-field">
                                        <div class="field-label">Sistema</div>
                                        <div class="field-value">
                                            {{ strtoupper($item->sistema_competencia ?: 'tradicional') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="torneo-view-field">
                                        <div class="field-label">Modalidad puntaje</div>
                                        <div class="field-value">{{ $item->modalidad_puntaje ?: 'No registrada' }}
                                        </div>
                                    </div>
                                </div>                                

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Organiza</div>
                                            <div class="field-value">{{ $item->organiza ?: 'No registrado' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Dirección</div>
                                            <div class="field-value">{{ $item->direccion ?: 'Sin dirección' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Inscripción de Organización</div>
                                            <div class="field-value">
                                                {{ number_format((float) $item->costo_inscripcion_organizacion, 2) }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Inscripción de Competidores</div>
                                            <div class="field-value">
                                                {{ number_format((float) $item->costo_inscripcion_competidor, 2) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="torneo-view-field">
                                            <div class="field-label">Estado</div>
                                            <div class="field-value">
                                                @if ($item->status == 1)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>                                    

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background: #f2f6ff; border-top: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-{{ $item->id }}" tabindex="-1"
        aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form method="POST" action="{{ route('torneos.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_torneo" value="{{ $item->id }}">

                <div class="modal-content rounded-4 overflow-hidden">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold" id="modalEditLabel{{ $item->id }}">Editando Torneo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-4" style="background: #f2f6ff;">
                        <div class="torneo-form-card">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="text-center mb-4">
                                        <label class="form-label d-block">Logo Torneo</label>
                                        <img id="logo_preview_edit_{{ $item->id }}" src="{{ $logoPreviewUrl }}"
                                            alt="{{ $item->nombre ?: 'Logo torneo' }}"
                                            class="torneo-logo-preview mx-auto mb-3"
                                            onerror="this.src='{{ asset('images/icono.png') }}'">
                                    </div>
                                    <div class="mb-3">
                                        <label for="logo_edit_{{ $item->id }}" class="form-label">Seleccionar
                                            archivo</label>
                                        <input type="file" name="logo" id="logo_edit_{{ $item->id }}"
                                            class="form-control @if (old('editing_torneo') == $item->id) @error('logo') is-invalid @enderror @endif"
                                            accept="image/jpeg,image/png,image/webp"
                                            onchange="previewTorneoLogo(this, 'logo_preview_edit_{{ $item->id }}')">
                                        @if (old('editing_torneo') == $item->id)
                                            @error('logo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" name="status" id="status_edit_{{ $item->id }}"
                                            value="1" class="form-check-input"
                                            {{ old('editing_torneo') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
                                        <label for="status_edit_{{ $item->id }}"
                                            class="form-check-label">Activo</label>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nombre_edit_{{ $item->id }}"
                                                class="form-label">Nombre</label>
                                            <input type="text" name="nombre"
                                                id="nombre_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('nombre') : $item->nombre }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('nombre') is-invalid @enderror @endif"
                                                maxlength="255" required>
                                            @if (old('editing_torneo') == $item->id)
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label for="ciudad_edit_{{ $item->id }}"
                                                class="form-label">Ciudad</label>
                                            <input type="text" name="ciudad"
                                                id="ciudad_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('ciudad') : ($item->ciudad ?: $item->lugar) }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('ciudad') is-invalid @enderror @endif"
                                                maxlength="255">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('ciudad')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-8">
                                            <label for="direccion_edit_{{ $item->id }}"
                                                class="form-label">Direccion</label>
                                            <input type="text" name="direccion"
                                                id="direccion_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('direccion') : $item->direccion }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('direccion') is-invalid @enderror @endif"
                                                maxlength="1000">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('direccion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="sistema_competencia_edit_{{ $item->id }}"
                                                class="form-label">Sistema competencia</label>
                                            @php $sistemaActual = old('editing_torneo') == $item->id ? old('sistema_competencia') : ($item->sistema_competencia ?: 'tradicional'); @endphp
                                            <select name="sistema_competencia"
                                                id="sistema_competencia_edit_{{ $item->id }}"
                                                class="form-select @if (old('editing_torneo') == $item->id) @error('sistema_competencia') is-invalid @enderror @endif"
                                                required>
                                                <option value="tradicional"
                                                    {{ $sistemaActual === 'tradicional' ? 'selected' : '' }}>
                                                    Tradicional</option>
                                                <option value="wkf"
                                                    {{ $sistemaActual === 'wkf' ? 'selected' : '' }}>WKF</option>
                                                <option value="otro"
                                                    {{ $sistemaActual === 'otro' ? 'selected' : '' }}>Otro</option>
                                            </select>
                                            @if (old('editing_torneo') == $item->id)
                                                @error('sistema_competencia')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label for="modalidad_puntaje_edit_{{ $item->id }}"
                                                class="form-label">Modalidad de puntaje</label>
                                            <input type="text" name="modalidad_puntaje"
                                                id="modalidad_puntaje_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('modalidad_puntaje') : $item->modalidad_puntaje }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('modalidad_puntaje') is-invalid @enderror @endif"
                                                maxlength="100">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('modalidad_puntaje')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label for="organiza_edit_{{ $item->id }}"
                                                class="form-label">Organiza</label>
                                            <input type="text" name="organiza"
                                                id="organiza_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('organiza') : $item->organiza }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('organiza') is-invalid @enderror @endif"
                                                maxlength="255">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('organiza')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="fecha_inicio_edit_{{ $item->id }}"
                                                class="form-label">Fecha inicio</label>
                                            <input type="date" name="fecha_inicio"
                                                id="fecha_inicio_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('fecha_inicio') : optional($item->fecha_inicio)->format('Y-m-d') }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('fecha_inicio') is-invalid @enderror @endif">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('fecha_inicio')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="fecha_fin_edit_{{ $item->id }}" class="form-label">Fecha
                                                fin</label>
                                            <input type="date" name="fecha_fin"
                                                id="fecha_fin_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('fecha_fin') : optional($item->fecha_fin)->format('Y-m-d') }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('fecha_fin') is-invalid @enderror @endif">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('fecha_fin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Estado</label>
                                            <div class="form-check form-switch mt-2">
                                                <input type="checkbox" name="status"
                                                    id="status_edit_secondary_{{ $item->id }}" value="1"
                                                    class="form-check-input"
                                                    {{ old('editing_torneo') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
                                                <label for="status_edit_secondary_{{ $item->id }}"
                                                    class="form-check-label ms-2">Activo</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="costo_inscripcion_organizacion_edit_{{ $item->id }}"
                                                class="form-label">Inscripcion de Organizacion</label>
                                            <input type="number" name="costo_inscripcion_organizacion"
                                                id="costo_inscripcion_organizacion_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('costo_inscripcion_organizacion') : $item->costo_inscripcion_organizacion }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('costo_inscripcion_organizacion') is-invalid @enderror @endif"
                                                min="0" step="0.01">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('costo_inscripcion_organizacion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label for="costo_inscripcion_competidor_edit_{{ $item->id }}"
                                                class="form-label">Inscripcion de Competidores</label>
                                            <input type="number" name="costo_inscripcion_competidor"
                                                id="costo_inscripcion_competidor_edit_{{ $item->id }}"
                                                value="{{ old('editing_torneo') == $item->id ? old('costo_inscripcion_competidor') : $item->costo_inscripcion_competidor }}"
                                                class="form-control @if (old('editing_torneo') == $item->id) @error('costo_inscripcion_competidor') is-invalid @enderror @endif"
                                                min="0" step="0.01">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('costo_inscripcion_competidor')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-12">
                                            <label for="persona_edit_{{ $item->id }}"
                                                class="form-label">Responsable</label>
                                            <select name="persona_id" id="persona_edit_{{ $item->id }}"
                                                class="form-select @if (old('editing_torneo') == $item->id) @error('persona_id') is-invalid @enderror @endif"
                                                required>
                                                <option value="">Seleccione</option>
                                                @foreach ($personas as $persona)
                                                    <option value="{{ $persona->id }}"
                                                        {{ (old('editing_torneo') == $item->id ? old('persona_id') : $item->persona_id) == $persona->id ? 'selected' : '' }}>
                                                        {{ $persona->first_name }}{{ $persona->ci ? ' - CI ' . $persona->ci : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if (old('editing_torneo') == $item->id)
                                                @error('persona_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-3" style="background: #f2f6ff; border-top: 0;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Actualizar
                        </button>
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

        @if ($errors->any() && old('editing_torneo'))
            let editModal = document.getElementById('modal-edit-{{ old('editing_torneo') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });
</script>
