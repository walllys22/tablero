<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="width: 90px; text-align: center">ID</th>
                    <th style="text-align: center">Nombre</th>
                    <th style="text-align: center">Descripcion</th>
                    <th style="width: 120px; text-align: center">Usuarios</th>
                    <th style="width: 120px; text-align: center">Estado</th>
                    <th style="width: 180px; text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td class="text-center">{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->description ?: 'Sin descripcion' }}</td>
                        <td class="text-center">{{ $item->users_count }}</td>
                        <td class="text-center">
                            @if ($item->status == 1)
                                <label class="label label-success">Activo</label>
                            @else
                                <label class="label label-danger">Inactivo</label>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($item->status == 1)
                                <button type="button" title="Inactivar" data-bs-toggle="modal" data-bs-target="#modal-status-role-{{ $item->id }}" class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('roles.toggle-status', $item) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Activar" class="btn btn-sm btn-warning text-white">
                                        <i class="bi bi-toggle-off"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" title="Ver" data-bs-toggle="modal" data-bs-target="#modal-view-role-{{ $item->id }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-edit-role-{{ $item->id }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-pencil-square"></i>
                            </button>
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
    <div class="modal fade" id="modal-view-role-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Datos del rol</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3" style="background: #f8f9fa;">
                    <div class="row g-3">
                        <div class="col-md-4"><div class="px-3 py-2 bg-white rounded-3 border"><strong>ID</strong><br>{{ $item->id }}</div></div>
                        <div class="col-md-4"><div class="px-3 py-2 bg-white rounded-3 border"><strong>Nombre</strong><br>{{ $item->name }}</div></div>
                        <div class="col-md-4"><div class="px-3 py-2 bg-white rounded-3 border"><strong>Usuarios</strong><br>{{ $item->users_count }}</div></div>
                        <div class="col-md-4"><div class="px-3 py-2 bg-white rounded-3 border"><strong>Estado</strong><br>{{ $item->status == 1 ? 'Activo' : 'Inactivo' }}</div></div>
                        <div class="col-md-12"><div class="px-3 py-2 bg-white rounded-3 border"><strong>Descripcion</strong><br>{{ $item->description ?: 'Sin descripcion' }}</div></div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-status-role-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form method="POST" action="{{ route('roles.toggle-status', $item) }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark fw-bold">
                        <h5 class="modal-title">Alerta</h5>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">Esta seguro de cambiar el estado?</div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-danger">Si</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-role-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('roles.update', $item) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_role" value="{{ $item->id }}">
                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title fw-bold">Editar rol</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name_edit_{{ $item->id }}" class="form-label">Nombre</label>
                            <input type="text" name="name" id="name_edit_{{ $item->id }}" value="{{ old('editing_role') == $item->id ? old('name') : $item->name }}" class="form-control @if(old('editing_role') == $item->id) @error('name') is-invalid @enderror @endif" maxlength="255" required>
                            @if (old('editing_role') == $item->id)
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="description_edit_{{ $item->id }}" class="form-label">Descripcion</label>
                            <input type="text" name="description" id="description_edit_{{ $item->id }}" value="{{ old('editing_role') == $item->id ? old('description') : $item->description }}" class="form-control @if(old('editing_role') == $item->id) @error('description') is-invalid @enderror @endif" maxlength="255">
                            @if (old('editing_role') == $item->id)
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="status" id="status_edit_{{ $item->id }}" value="1" class="form-check-input" {{ old('editing_role') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
                            <label for="status_edit_{{ $item->id }}" class="form-check-label">Activo</label>
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

        @if ($errors->any() && old('editing_role'))
            let editModal = document.getElementById('modal-edit-role-{{ old('editing_role') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });
</script>
