@php
    $katas = $data;
@endphp

<div class="col-md-12">
    @if ($sistemas->isNotEmpty())
        <ul class="nav nav-tabs katas-tabs mb-3" id="katas-system-tabs" role="tablist">
            @foreach ($sistemas as $sistema)
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link {{ $sistema->id === $activeSystemId ? 'active' : '' }}"
                        id="tab-sistema-{{ $sistema->id }}"
                        data-sistema-id="{{ $sistema->id }}"
                        data-bs-toggle="tab"
                        data-bs-target="#panel-sistema-{{ $sistema->id }}"
                        type="button"
                        role="tab"
                        aria-controls="panel-sistema-{{ $sistema->id }}"
                        aria-selected="{{ $sistema->id === $activeSystemId ? 'true' : 'false' }}"
                    >
                        <i class="fa-solid fa-sitemap me-1"></i> {{ $sistema->nombre }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="katas-system-tabs-content">
            @foreach ($sistemas as $sistema)
                <div
                    class="tab-pane fade {{ $sistema->id === $activeSystemId ? 'show active' : '' }}"
                    id="panel-sistema-{{ $sistema->id }}"
                    role="tabpanel"
                    aria-labelledby="tab-sistema-{{ $sistema->id }}"
                    tabindex="0"
                >
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover eventos-table">
                            <thead>
                                <tr>
                                    <th style="width: 140px; text-align: center">Nro. Kata</th>
                                    <th style="text-align: center">Nombre</th>
                                    <th style="width: 140px; text-align: center">Estado</th>
                                    <th style="width: 190px; text-align: center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($sistema->id === $activeSystemId)
                                @forelse ($data as $kata)
                                    <tr data-kata-row data-kata-id="{{ $kata->id }}" data-sistema-id="{{ $kata->sistema_id }}" data-numero="{{ $kata->numero ?? $kata->id }}" data-nombre="{{ e($kata->nombre) }}" data-estado="{{ $kata->estado }}">
                                        <td style="text-align: center; vertical-align: middle;">{{ $kata->numero ?? $kata->id }}</td>
                                        <td style="vertical-align: middle;">{{ $kata->nombre }}</td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            @if ($kata->estado === 'Activo')
                                                <label class="label label-success">Activo</label>
                                            @else
                                                <label class="label label-danger">Inactivo</label>
                                            @endif
                                        </td>
                                        <td style="vertical-align: middle;" class="text-center">
                                            @if ($kata->estado === 'Activo')
                                                <button type="button" title="Inactivar" data-bs-toggle="modal" data-bs-target="#modal-status-{{ $kata->id }}" class="btn btn-sm btn-warning text-white">
                                                    <i class="bi bi-toggle-on"></i>
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('katas.toggle-status', $kata) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" title="Activar" class="btn btn-sm btn-warning text-white">
                                                        <i class="bi bi-toggle-off"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" title="Ver" data-bs-toggle="modal" data-bs-target="#modal-view-{{ $kata->id }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $kata->id }}" class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="#" onclick="event.preventDefault(); deleteItem('{{ route('katas.destroy', $kata) }}')" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No hay katas registrados para este sistema.</td>
                                    </tr>
                                @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if ($sistema->id === $activeSystemId)
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
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <h5 class="text-center eventos-empty">
            <img src="{{ asset('images/empty.png') }}" width="120" alt="Sin resultados">
            <br><br>
            No hay resultados
        </h5>
    @endif
</div>

<div class="d-none" id="katas-order-data">
    @foreach ($katasOrden as $kataOrden)
        <span
            data-kata-order-row
            data-kata-id="{{ $kataOrden->id }}"
            data-sistema-id="{{ $kataOrden->sistema_id }}"
            data-numero="{{ $kataOrden->numero ?? $kataOrden->id }}"
            data-nombre="{{ e($kataOrden->nombre) }}"
            data-estado="{{ $kataOrden->estado }}"
        ></span>
    @endforeach
</div>

@foreach ($katas as $item)
    <div class="modal fade" id="modal-status-{{ $item->id }}" tabindex="-1" aria-labelledby="modalStatusLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form method="POST" action="{{ route('katas.toggle-status', $item) }}">
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
                    <h5 class="modal-title fw-bold" id="modalViewLabel{{ $item->id }}">Datos del kata</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3" style="background: #f8f9fa;">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="h-100 px-3 py-2 bg-white rounded-3 border">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Nro. Kata</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->numero ?? $item->id }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="h-100 px-3 py-2 bg-white rounded-3 border">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Nombre</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->nombre }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="h-100 px-3 py-2 bg-white rounded-3 border">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Sistema</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->sistema?->nombre ?: 'Sin sistema' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="h-100 px-3 py-2 bg-white rounded-3 border">
                                <div class="fw-bold" style="font-size: 12px; line-height: 1;">Estado</div>
                                <div class="fw-semibold" style="font-size: 14px;">{{ $item->estado }}</div>
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
            <form method="POST" action="{{ route('katas.update', $item) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_kata" value="{{ $item->id }}">

                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title fw-bold" id="modalEditLabel{{ $item->id }}">Editar kata</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="numero_edit_{{ $item->id }}" class="form-label">Nro. Kata</label>
                                <input type="number" name="numero" id="numero_edit_{{ $item->id }}" value="{{ old('editing_kata') == $item->id ? old('numero') : ($item->numero ?? $item->id) }}" class="form-control @if(old('editing_kata') == $item->id) @error('numero') is-invalid @enderror @endif" min="1" required>
                                @if (old('editing_kata') == $item->id)
                                    @error('numero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="nombre_edit_{{ $item->id }}" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre_edit_{{ $item->id }}" value="{{ old('editing_kata') == $item->id ? old('nombre') : $item->nombre }}" class="form-control @if(old('editing_kata') == $item->id) @error('nombre') is-invalid @enderror @endif" maxlength="255" required data-kata-name-field data-numero-field="#numero_edit_{{ $item->id }}" data-sistema-field="#sistema_edit_{{ $item->id }}" data-kata-id="{{ $item->id }}">
                                @if (old('editing_kata') == $item->id)
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label for="sistema_edit_{{ $item->id }}" class="form-label">Sistema</label>
                                @php
                                    $sistemaActual = old('editing_kata') == $item->id ? old('sistema_id') : $item->sistema_id;
                                @endphp
                                <select name="sistema_id" id="sistema_edit_{{ $item->id }}" class="form-select @if(old('editing_kata') == $item->id) @error('sistema_id') is-invalid @enderror @endif" required>
                                    @foreach ($sistemasDisponibles as $sistema)
                                        <option value="{{ $sistema->id }}" {{ $sistemaActual == $sistema->id ? 'selected' : '' }}>
                                            {{ $sistema->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (old('editing_kata') == $item->id)
                                    @error('sistema_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label for="estado_edit_{{ $item->id }}" class="form-label">Estado</label>
                                @php
                                    $estadoActual = old('editing_kata') == $item->id ? old('estado') : $item->estado;
                                @endphp
                                <select name="estado" id="estado_edit_{{ $item->id }}" class="form-select @if(old('editing_kata') == $item->id) @error('estado') is-invalid @enderror @endif" required>
                                    <option value="Activo" {{ $estadoActual === 'Activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="Inactivo" {{ $estadoActual === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @if (old('editing_kata') == $item->id)
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
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
                list(url.searchParams.get('page') || 1, '{{ $activeSystemId }}');
            }
        });

        @if ($errors->any() && old('editing_kata'))
            let editModal = document.getElementById('modal-edit-{{ old('editing_kata') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });
</script>
