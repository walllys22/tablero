<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="text-align: center">ID</th>
                    <th style="text-align: center">Organizacion</th>
                    <th style="text-align: center">Persona</th>
                    <th style="text-align: center">Inscripciones</th>
                    <th style="text-align: center">Estado</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">
                            {{ $item->id }}
                        </td>
                        <td style="vertical-align: middle;">
                            <strong>{{ $item->nombre }}</strong>
                        </td>
                        <td style="vertical-align: middle;">
                            @if ($item->persona)
                                <strong>{{ $item->persona->first_name }}</strong><br>
                                <small class="text-muted">{{ $item->persona->ci ? 'CI: ' . $item->persona->ci : 'Sin CI' }}</small>
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
                                <button type="button" title="Inactivar" data-bs-toggle="modal" data-bs-target="#modal-status-{{ $item->id }}" class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('organizaciones.toggle-status', $item) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Activar" class="btn btn-sm btn-warning text-white">
                                        <i class="bi bi-toggle-off"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" title="Ver" data-bs-toggle="modal" data-bs-target="#modal-view-{{ $item->id }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $item->id }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="#" onclick="event.preventDefault(); deleteItem('{{ route('organizaciones.destroy', $item) }}')" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete" class="btn btn-sm btn-danger">
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
            @if(count($data) > 0)
                <p class="text-muted mb-md-0">Mostrando del {{ $data->firstItem() }} al {{ $data->lastItem() }} de {{ $data->total() }} registros.</p>
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
    @if ($item->status == 1)
        <div class="modal fade" id="modal-status-{{ $item->id }}" tabindex="-1" aria-labelledby="modalStatusLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form method="POST" action="{{ route('organizaciones.toggle-status', $item) }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark fw-bold">
                            <h5 class="modal-title" id="modalStatusLabel{{ $item->id }}">Alerta</h5>
                            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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

    <div class="modal fade" id="modal-view-{{ $item->id }}" tabindex="-1" aria-labelledby="modalViewLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header py-2" style="background: #3a19f5; border-bottom: 0;">
                    <h5 class="modal-title fw-bold text-white" id="modalViewLabel{{ $item->id }}" style="font-size: 20px; color: white;">
                        Datos de Organizacion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-2" style="background: #eeeeee;">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Organizacion</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->nombre }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Persona</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->persona ? $item->persona->first_name : 'Sin persona' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Estado</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->status == 1 ? 'Activo' : 'Inactivo' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Inscripciones</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->inscripciones_count }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Fecha de registro</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->created_at ? $item->created_at->format('d/m/Y') : 'No registrada' }}</div>
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

    <div class="modal fade" id="modal-edit-{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('organizaciones.update', $item) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_organizacion" value="{{ $item->id }}">

                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title fw-bold" id="modalEditLabel{{ $item->id }}">Editar organizacion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre_edit_{{ $item->id }}" class="form-label">Organizacion</label>
                                <input type="text" name="nombre" id="nombre_edit_{{ $item->id }}" value="{{ old('editing_organizacion') == $item->id ? old('nombre') : $item->nombre }}" class="form-control @if(old('editing_organizacion') == $item->id) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('editing_organizacion') == $item->id)
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="persona_edit_{{ $item->id }}" class="form-label">Persona</label>
                                <select name="persona_id" id="persona_edit_{{ $item->id }}" class="form-select @if(old('editing_organizacion') == $item->id) @error('persona_id') is-invalid @enderror @endif" required>
                                    <option value="">Seleccione</option>
                                    @foreach ($personas as $persona)
                                        <option value="{{ $persona->id }}" {{ (old('editing_organizacion') == $item->id ? old('persona_id') : $item->persona_id) == $persona->id ? 'selected' : '' }}>
                                            {{ $persona->first_name }}{{ $persona->ci ? ' - CI ' . $persona->ci : '' }}
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
                                    <input type="checkbox" name="status" id="status_edit_{{ $item->id }}" value="1" class="form-check-input" {{ old('editing_organizacion') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
                                    <label for="status_edit_{{ $item->id }}" class="form-check-label">Activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white">
                            <i class="bi bi-check-lg"></i> Actualizar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach

<script>
    $(document).ready(function () {
        $('.page-link').click(function (event) {
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
</script>
