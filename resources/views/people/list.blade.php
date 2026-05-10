<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="text-align: center">Documento</th>
                    <th style="text-align: center">Nombre completo</th>
                    <th style="text-align: center">Datos personales</th>
                    <th style="text-align: center">Contacto</th>
                    <th style="text-align: center">Estado</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $defaultPersonImage = asset('images/default.jpg');
                        $image = $item->image_url;

                        $age = $item->birth_date ? $item->birth_date->diffInYears(now()) : null;
                        $whatsappPhone = preg_replace('/\D+/', '', $item->phone ?? '');
                    @endphp

                    <tr>
                        <td style="text-align: center; vertical-align: middle;">
                            <strong>CI</strong><br>
                            <span>{{ $item->ci ?: 'Sin documento' }}</span>
                        </td>
                        <td style="vertical-align: middle;">
                            <div style="display: flex; align-items: center;">
                                <img src="{{ $image }}" alt="{{ $item->first_name ?: 'Persona' }}" class="image-expandable" style="width: 60px; height: 60px; border-radius: 30px; margin-right: 10px; object-fit: cover;" onerror="this.src='{{ $defaultPersonImage }}'">
                                <div>
                                    <strong>{{ strtoupper($item->first_name ?: 'Sin nombre') }}</strong><br>
                                    <small class="text-muted">ID: {{ $item->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->birth_date)
                                <div><strong>Nacimiento:</strong> {{ $item->birth_date->format('d/m/Y') }}</div>
                                <small>{{ $age }} años</small><br>
                            @else
                                <span class="text-muted">Sin fecha de nacimiento</span><br>
                            @endif
                            <small><strong>Genero:</strong> {{ $item->gender ?: 'No registrado' }}</small><br>
                            <small><strong>Tipo sangre:</strong> {{ $item->sangre ?: 'No registrado' }}</small>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->phone)
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <span style="font-weight: bold; font-size: 13px; white-space: nowrap;">{{ $item->phone }}</span>
                                    @if ($whatsappPhone)
                                        <a href="https://wa.me/{{ $whatsappPhone }}?text=Hola {{ urlencode($item->first_name) }}" target="_blank" class="label label-success" style="margin-top: 5px; padding: 3px 8px; font-size: 10px; text-decoration: none; cursor: pointer;">
                                            WhatsApp
                                        </a>
                                    @endif
                                    @if ($item->email)
                                        <small style="margin-top: 5px; display: block;">{{ $item->email }}</small>
                                    @endif
                                    @if ($item->address)
                                        <small class="text-muted" style="margin-top: 4px; display: block;">{{ $item->address }}</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted" style="font-style: italic;">No registrado</span>
                                @if ($item->email)
                                    <br><small>{{ $item->email }}</small>
                                @endif
                                @if ($item->address)
                                    <br><small class="text-muted">{{ $item->address }}</small>
                                @endif
                            @endif
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
                                <form method="POST" action="{{ route('people.toggle-status', $item) }}" class="d-inline">
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

                            <a href="#" onclick="event.preventDefault(); deleteItem('{{ route('people.destroy', $item) }}')" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <h5 class="text-center" style="margin-top: 50px">
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
        $defaultPersonImage = asset('images/default.jpg');
        $modalPersonImage = $item->image_url;
    @endphp

    @if ($item->status == 1)
        <div class="modal fade" id="modal-status-{{ $item->id }}" tabindex="-1" aria-labelledby="modalStatusLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form method="POST" action="{{ route('people.toggle-status', $item) }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark fw-bold">
                            <h5 class="modal-title" id="modalStatusLabel{{ $item->id }}">Alerta</h5>
                            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center">
                            Esta seguro de desactivar la persona?
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
                        Datos Personales
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-2" style="background: #eeeeee;">
                    <div class="d-flex gap-2 align-items-stretch flex-column flex-md-row">
                        <div class="flex-shrink-0 overflow-hidden" style="width: 165px; min-height: 170px; border-radius: 18px; background: #ff1717;">
                            <img src="{{ $modalPersonImage }}" alt="{{ $item->first_name ?: 'Persona' }}" style="width: 100%; height: 100%; min-height: 170px; object-fit: cover;" onerror="this.src='{{ $defaultPersonImage }}'">
                        </div>

                        <div class="flex-grow-1">
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Nombre Completo</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->first_name ?: 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Fecha de Ingreso</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->created_at ? $item->created_at->format('d/m/Y') : 'No registrado' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Email</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->email ?: 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Documento</div>
                                        <div class="fw-semibold" style="font-size: 14px;">CI: {{ $item->ci ?: 'Sin documento' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Genero</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->gender ?: 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Fecha de Nacimiento</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->birth_date ? $item->birth_date->format('d/m/Y') : 'No registrado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-100 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Telefono / Celular</div>
                                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->phone ?: 'No registrado' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 px-3 py-2" style="background: #f8f8f8; border-radius: 8px;">
                        <div class="fw-bold" style="font-size: 12px; line-height: 1;">Direccion</div>
                        <div class="fw-semibold" style="font-size: 14px;">{{ $item->address ?: 'No registrada' }}</div>
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
            <form method="POST" action="{{ route('people.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_persona" value="{{ $item->id }}">

                <div class="modal-content">
                    <div class="modal-header bg-info text-dark" >
                        <h5 class="modal-title fw-bold" id="modalEditLabel{{ $item->id }}" >Editar persona</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        @include('people.partials.form', ['persona' => $item, 'editing' => true])
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

        @if ($errors->any() && old('editing_persona'))
            let editModal = document.getElementById('modal-edit-{{ old('editing_persona') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });
</script>
