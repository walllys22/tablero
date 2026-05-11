<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="text-align: center">ID</th>
                    <th style="text-align: center">Nombre</th>
                    <th style="text-align: center">Descripción</th>
                    <th style="text-align: center">Estado</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">{{ $item->id }}</td>
                        <td style="vertical-align: middle;">{{ $item->nombre }}</td>
                        <td style="vertical-align: middle;">{{ $item->descripcion ?: 'Sin descripción' }}</td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->status == 1)
                                <label class="label label-success">Activo</label>
                            @else
                                <label class="label label-danger">Inactivo</label>
                            @endif
                        </td>
                        <td style="vertical-align: middle; width: 16%" class="text-end">
                            @if ($item->status == 1)
                                <button type="button" title="Inactivar" data-bs-toggle="modal" data-bs-target="#modal-status-{{ $item->id }}" class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('estiloskarate.toggle-status', $item) }}" class="d-inline">
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
                            <a href="#" onclick="event.preventDefault(); deleteItem('{{ route('estiloskarate.destroy', $item) }}')" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
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
    <div class="modal fade" id="modal-status-{{ $item->id }}" tabindex="-1" aria-labelledby="modalStatusLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form method="POST" action="{{ route('estiloskarate.toggle-status', $item) }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark fw-bold">
                        <h5 class="modal-title" id="modalStatusLabel{{ $item->id }}">Alerta</h5>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                        Esta seguro de cambiar el estado?
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-danger">Si</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal-view-{{ $item->id }}" tabindex="-1" aria-labelledby="modalViewLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header py-2 bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalViewLabel{{ $item->id }}">Datos de Estilo Karate</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3" style="background: #f8f9fa;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="h-100 px-3 py-2 bg-white rounded-3 border">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Nombre</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->nombre }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="h-100 px-3 py-2 bg-white rounded-3 border">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Estado</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->status == 1 ? 'Activo' : 'Inactivo' }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="h-100 px-3 py-2 bg-white rounded-3 border">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Descripción</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->descripcion ?: 'Sin descripción' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="h-100 px-3 py-2 bg-white rounded-3 border">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Fecha de registro</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->created_at ? $item->created_at->format('d/m/Y') : 'No registrada' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('estiloskarate.update', $item) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_estilo" value="{{ $item->id }}">

                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title fw-bold" id="modalEditLabel{{ $item->id }}">Editar Estilo Karate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre_edit_{{ $item->id }}" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre_edit_{{ $item->id }}" value="{{ old('editing_estilo') == $item->id ? old('nombre') : $item->nombre }}" class="form-control @if(old('editing_estilo') == $item->id) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('editing_estilo') == $item->id)
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="descripcion_edit_{{ $item->id }}" class="form-label">Descripción</label>
                                <input type="text" name="descripcion" id="descripcion_edit_{{ $item->id }}" value="{{ old('editing_estilo') == $item->id ? old('descripcion') : $item->descripcion }}" class="form-control @if(old('editing_estilo') == $item->id) @error('descripcion') is-invalid @enderror @endif" maxlength="255">
                                @if (old('editing_estilo') == $item->id)
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="status" id="status_edit_{{ $item->id }}" value="1" class="form-check-input" {{ old('editing_estilo') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
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

        @if ($errors->any() && old('editing_estilo'))
            let editModal = document.getElementById('modal-edit-{{ old('editing_estilo') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });
</script>
