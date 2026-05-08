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
                                @if ($item->logo)
                                    <img src="{{ $logoPreviewUrl }}" alt="{{ $item->nombre ?: 'Torneo' }}" class="image-expandable eventos-logo" style="object-fit: contain; background: #f8f9fa;" onerror="this.src='{{ asset('images/icono.png') }}'">
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
                            <a href="{{ route('modalidades.index', ['torneo' => $item, 'return' => 'torneos']) }}" title="Modalidades" class="btn btn-sm btn-primary">
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
                            <button type="button" title="Ver" data-bs-toggle="modal" data-bs-target="#modal-view-{{ $item->id }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                            </button>
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
    @php
        $logoUrl = $item->logo
            ? asset('storage/' . ltrim($item->logo, '/'))
            : asset('images/icono.png');
        $logoVersion = optional($item->updated_at)->timestamp ?? $item->id;
        $logoPreviewUrl = $item->logo ? $logoUrl . '?v=' . $logoVersion : $logoUrl;
    @endphp

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

    <div class="modal fade" id="modal-view-{{ $item->id }}" tabindex="-1" aria-labelledby="modalViewLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-1">
                    <h5 class="modal-title fw-bold mb-0" id="modalViewLabel{{ $item->id }}">Viendo Torneo</h5>
                    <button type="button" class="btn fw-bold px-4" style="background: #ffc000; color: #000; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.45);" data-bs-dismiss="modal">Cerrar</button>
                </div>
                <div class="modal-body px-4 pt-1 pb-4">
                    <div class="px-4 py-5" style="border: 1.5px solid #1f4da1; border-radius: 40px;">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-3 text-center">
                                <img src="{{ $logoPreviewUrl }}" alt="{{ $item->nombre ?: 'Logo torneo' }}" style="width: 138px; height: 138px; object-fit: contain; border: 1px solid #333; background: #f8f9fa;" onerror="this.src='{{ asset('images/icono.png') }}'">
                                <div class="mt-2 fw-semibold" style="font-size: 13px;">Logo Torneo</div>
                            </div>

                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label mb-1">Nombre</label>
                                        <input type="text" value="{{ $item->nombre ?: 'Sin nombre' }}" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label mb-1">Lugar</label>
                                        <input type="text" value="{{ $item->lugar ?: 'Sin lugar' }}" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Fecha inicio</label>
                                        <input type="date" value="{{ optional($item->fecha_inicio)->format('Y-m-d') }}" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Fecha fin</label>
                                        <input type="date" value="{{ optional($item->fecha_fin)->format('Y-m-d') }}" class="form-control" readonly>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" {{ $item->status == 1 ? 'checked' : '' }} disabled>
                                            <label class="form-check-label">Activo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form method="POST" action="{{ route('torneos.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_torneo" value="{{ $item->id }}">

                <div class="modal-content border-0" style="background: #ffffff;">
                    <div class="px-4 pt-3 pb-2">
                        <h5 class="modal-title fw-bold text-center mb-0" id="modalEditLabel{{ $item->id }}">Editando Torneo</h5>
                    </div>
                    <div class="modal-body px-4 pt-3 pb-2">
                        <div class="px-4 py-5" style="border: 1.5px solid #1f4da1; border-radius: 40px;">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-3 text-center">
                                    <img id="logo_preview_edit_{{ $item->id }}" src="{{ $logoPreviewUrl }}" alt="{{ $item->nombre ?: 'Logo torneo' }}" style="width: 138px; height: 138px; object-fit: contain; border: 1px solid #333; background: #f8f9fa;" onerror="this.src='{{ asset('images/icono.png') }}'">
                                    <div class="mt-2 fw-semibold" style="font-size: 13px;">Logo Torneo</div>
                                    <input type="file" name="logo" id="logo_edit_{{ $item->id }}" class="form-control form-control-sm mt-2 @if(old('editing_torneo') == $item->id) @error('logo') is-invalid @enderror @endif" accept="image/jpeg,image/png,image/webp" onchange="previewTorneoLogo(this, 'logo_preview_edit_{{ $item->id }}')">
                                    @if (old('editing_torneo') == $item->id)
                                        @error('logo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="col-md-9">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label for="nombre_edit_{{ $item->id }}" class="form-label mb-1">Nombre</label>
                                            <input type="text" name="nombre" id="nombre_edit_{{ $item->id }}" value="{{ old('editing_torneo') == $item->id ? old('nombre') : $item->nombre }}" class="form-control @if(old('editing_torneo') == $item->id) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                            @if (old('editing_torneo') == $item->id)
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="lugar_edit_{{ $item->id }}" class="form-label mb-1">Lugar</label>
                                            <input type="text" name="lugar" id="lugar_edit_{{ $item->id }}" value="{{ old('editing_torneo') == $item->id ? old('lugar') : $item->lugar }}" class="form-control @if(old('editing_torneo') == $item->id) @error('lugar') is-invalid @enderror @endif" maxlength="255">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('lugar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label for="fecha_inicio_edit_{{ $item->id }}" class="form-label mb-1">Fecha inicio</label>
                                            <input type="date" name="fecha_inicio" id="fecha_inicio_edit_{{ $item->id }}" value="{{ old('editing_torneo') == $item->id ? old('fecha_inicio') : optional($item->fecha_inicio)->format('Y-m-d') }}" class="form-control @if(old('editing_torneo') == $item->id) @error('fecha_inicio') is-invalid @enderror @endif">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('fecha_inicio')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label for="fecha_fin_edit_{{ $item->id }}" class="form-label mb-1">Fecha fin</label>
                                            <input type="date" name="fecha_fin" id="fecha_fin_edit_{{ $item->id }}" value="{{ old('editing_torneo') == $item->id ? old('fecha_fin') : optional($item->fecha_fin)->format('Y-m-d') }}" class="form-control @if(old('editing_torneo') == $item->id) @error('fecha_fin') is-invalid @enderror @endif">
                                            @if (old('editing_torneo') == $item->id)
                                                @error('fecha_fin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" name="status" id="status_edit_{{ $item->id }}" value="1" class="form-check-input" {{ old('editing_torneo') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
                                                <label for="status_edit_{{ $item->id }}" class="form-check-label">Activo</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pt-2 pb-4">
                        <button type="submit" class="btn text-white fw-bold px-3" style="background: #5b9bd5; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.45);">
                            <i class="bi bi-check-lg"></i> Actualizar
                        </button>
                        <button type="button" class="btn fw-bold px-4" style="background: #ffc000; color: #000; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.45);" data-bs-dismiss="modal">Cerrar</button>
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
