<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="text-align: center">Documento</th>
                    <th style="text-align: center">Competidor</th>
                    <th style="text-align: center">Datos personales</th>
                    <th style="text-align: center">Contacto</th>
                    <th style="text-align: center">Estado</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $persona = $item->persona;
                        $image = $persona ? $persona->image_url : asset('images/default.jpg');
                        $age = $persona && $persona->birth_date ? $persona->birth_date->diffInYears(now()) : null;
                    @endphp
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">
                            <strong>CI</strong><br>
                            <span>{{ $persona->ci ?? 'Sin documento' }}</span>
                        </td>
                        <td style="vertical-align: middle;">
                            <div style="display: flex; align-items: center;">
                                <img src="{{ $image }}" alt="{{ $persona->first_name ?? 'Competidor' }}" class="image-expandable" style="width: 60px; height: 60px; border-radius: 30px; margin-right: 10px; object-fit: cover;" onerror="this.src='{{ asset('images/default.jpg') }}'">
                                <div>
                                    <strong>{{ strtoupper($persona->first_name ?? 'Sin nombre') }}</strong>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($persona && $persona->birth_date)
                                <div><strong>Nacimiento:</strong> {{ $persona->birth_date->format('d/m/Y') }}</div>
                                <small>{{ $age }} anos</small><br>
                            @else
                                <span class="text-muted">Sin fecha de nacimiento</span><br>
                            @endif
                            <small><strong>Genero:</strong> {{ $persona->gender ?? 'No registrado' }}</small><br>
                            <small><strong>Tipo sangre:</strong> {{ $persona->sangre ?? 'No registrado' }}</small><br>
                            <small><strong>Peso:</strong> {{ $item->peso !== null ? number_format((float) $item->peso, 3) . ' Kg' : 'No registrado' }}</small>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($persona && ($persona->phone || $persona->email || $persona->address))
                                @if ($persona->phone)
                                    <strong>{{ $persona->phone }}</strong><br>
                                @endif
                                @if ($persona->email)
                                    <small>{{ $persona->email }}</small><br>
                                @endif
                                @if ($persona->address)
                                    <small class="text-muted">{{ $persona->address }}</small>
                                @endif
                            @else
                                <span class="text-muted">No registrado</span>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->status == 1)
                                <label class="label label-success">Activo</label>
                            @else
                                <label class="label label-danger">Inactivo</label>
                            @endif
                        </td>
                        <td style="vertical-align: middle; width: 12%" class="text-end">
                            @if ($item->status == 1)
                                <button type="button" title="Inactivar" data-bs-toggle="modal" data-bs-target="#modal-status-{{ $item->id }}" class="btn btn-sm btn-warning text-white p-1">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('organizaciones.competidores.toggle-status', [$organizacion, $item]) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Activar" class="btn btn-sm btn-warning text-white p-1">
                                        <i class="bi bi-toggle-off"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" title="Ver" data-bs-toggle="modal" data-bs-target="#modal-view-{{ $item->id }}" class="btn btn-sm btn-primary p-1">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="#" onclick="event.preventDefault(); deleteItem('{{ route('organizaciones.competidores.destroy', [$organizacion, $item]) }}')" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete" class="btn btn-sm btn-danger p-1">
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
        $persona = $item->persona;
        $modalPersonImage = $persona ? $persona->image_url : asset('images/default.jpg');
    @endphp

    @if ($item->status == 1)
        <div class="modal fade" id="modal-status-{{ $item->id }}" tabindex="-1" aria-labelledby="modalStatusLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form method="POST" action="{{ route('organizaciones.competidores.toggle-status', [$organizacion, $item]) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark fw-bold">
                            <h5 class="modal-title" id="modalStatusLabel{{ $item->id }}">Alerta</h5>
                            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center">
                            Esta seguro de desactivar este competidor?
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
                        Datos del Competidor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-2" style="background: #eeeeee;">
                    <div class="d-flex gap-2 align-items-stretch flex-column flex-md-row">
                        <div class="flex-shrink-0 overflow-hidden" style="width: 165px; min-height: 170px; border-radius: 18px; background: #f8f8f8;">
                            <img src="{{ $modalPersonImage }}" alt="{{ $persona->first_name ?? 'Competidor' }}" style="width: 100%; height: 100%; min-height: 170px; object-fit: cover;" onerror="this.src='{{ asset('images/default.jpg') }}'">
                        </div>

                        <div class="flex-grow-1">
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Nombre Completo</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $persona->first_name ?? 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Documento</div>
                                        <div class="fw-semibold" style="font-size: 14px;">CI: {{ $persona->ci ?? 'Sin documento' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Genero</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $persona->gender ?? 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Nacimiento</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $persona && $persona->birth_date ? $persona->birth_date->format('d/m/Y') : 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Estado</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->status == 1 ? 'Activo' : 'Inactivo' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Peso</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->peso !== null ? number_format((float) $item->peso, 3) . ' Kg' : 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Telefono</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $persona->phone ?? 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Email</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $persona->email ?? 'No registrado' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Direccion</div>
                        <div class="fw-semibold" style="font-size: 14px;">{{ $persona->address ?? 'No registrada' }}</div>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background: #eeeeee; border-top: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
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
    });
</script>
