<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="text-align: center">Torneo</th>
                    <th style="text-align: center">Lugar</th>
                    <th style="text-align: center">Fecha inicio</th>
                    <th style="text-align: center">Fecha fin</th>
                    <th style="text-align: center">Estado</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td style="vertical-align: middle;">
                            <div class="eventos-name-cell">
                                @if ($item->logo)
                                    <img src="{{ asset('storage/' . $item->logo) }}" alt="{{ $item->nombre ?: 'Torneo' }}" class="image-expandable eventos-logo" onerror="this.remove()">
                                @endif
                                <div>
                                    <strong>{{ $item->nombre ?: 'Sin nombre' }}</strong><br>
                                    <small class="text-muted">ID: {{ $item->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            {{ $item->lugar ?: 'Sin lugar' }}
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->fecha_inicio)
                                <label class="label label-primary">{{ $item->fecha_inicio->format('d/m/Y') }}</label>
                            @else
                                <span class="text-muted">Sin fecha</span>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->fecha_fin)
                                <label class="label label-info">{{ $item->fecha_fin->format('d/m/Y') }}</label>
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
                        <td style="vertical-align: middle; width: 14%" class="no-sort no-click bread-actions text-end">
                            <a href="{{ route('modalidades.index', $item) }}" title="Modalidades" class="btn btn-sm btn-primary">
                                <i class="bi bi-list-check"></i>
                            </a>
                            @if ($item->status == 1)
                                <button type="button" title="Inactivar" data-bs-toggle="modal" data-bs-target="#modal-status-{{ $item->id }}" class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('torneos.toggle-status', $item) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Activar" class="btn btn-sm btn-warning text-white">
                                        <i class="bi bi-toggle-off"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $item->id }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="#" onclick="event.preventDefault(); deleteItem('{{ route('torneos.destroy', $item) }}')" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete" class="btn btn-sm btn-danger delete">
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
                <form method="POST" action="{{ route('torneos.toggle-status', $item) }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="modalStatusLabel{{ $item->id }}">Alerta</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center">
                            Esta seguro de desactivar el campeonato
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

    <div class="modal fade" id="modal-edit-{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('torneos.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_torneo" value="{{ $item->id }}">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel{{ $item->id }}">Editar torneo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nombre_edit_{{ $item->id }}" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre_edit_{{ $item->id }}" value="{{ old('editing_torneo') == $item->id ? old('nombre') : $item->nombre }}" class="form-control @if(old('editing_torneo') == $item->id) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('editing_torneo') == $item->id)
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="lugar_edit_{{ $item->id }}" class="form-label">Lugar</label>
                                <input type="text" name="lugar" id="lugar_edit_{{ $item->id }}" value="{{ old('editing_torneo') == $item->id ? old('lugar') : $item->lugar }}" class="form-control @if(old('editing_torneo') == $item->id) @error('lugar') is-invalid @enderror @endif" maxlength="255">
                                @if (old('editing_torneo') == $item->id)
                                    @error('lugar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_inicio_edit_{{ $item->id }}" class="form-label">Fecha inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio_edit_{{ $item->id }}" value="{{ old('editing_torneo') == $item->id ? old('fecha_inicio') : optional($item->fecha_inicio)->format('Y-m-d') }}" class="form-control @if(old('editing_torneo') == $item->id) @error('fecha_inicio') is-invalid @enderror @endif">
                                @if (old('editing_torneo') == $item->id)
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_fin_edit_{{ $item->id }}" class="form-label">Fecha fin</label>
                                <input type="date" name="fecha_fin" id="fecha_fin_edit_{{ $item->id }}" value="{{ old('editing_torneo') == $item->id ? old('fecha_fin') : optional($item->fecha_fin)->format('Y-m-d') }}" class="form-control @if(old('editing_torneo') == $item->id) @error('fecha_fin') is-invalid @enderror @endif">
                                @if (old('editing_torneo') == $item->id)
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-8">
                                <label for="logo_edit_{{ $item->id }}" class="form-label">Logo</label>
                                <input type="file" name="logo" id="logo_edit_{{ $item->id }}" class="form-control @if(old('editing_torneo') == $item->id) @error('logo') is-invalid @enderror @endif" accept="image/jpeg,image/png,image/webp">
                                @if ($item->logo)
                                    <small class="text-muted">Logo actual: {{ basename($item->logo) }}</small>
                                @endif
                                @if (old('editing_torneo') == $item->id)
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="status" id="status_edit_{{ $item->id }}" value="1" class="form-check-input" {{ old('editing_torneo') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
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

        @if ($errors->any() && old('editing_torneo'))
            let editModal = document.getElementById('modal-edit-{{ old('editing_torneo') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });
</script>
