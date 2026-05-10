<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="text-align: center">Organizacion</th>
                    <th style="text-align: center">Costo organizacion</th>
                    <th style="text-align: center">Competidores</th>
                    <th style="text-align: center">Total competidores</th>
                    <th style="text-align: center">Total general</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($organizaciones as $inscripcion)
                    @php
                        $totalCompetidores = $inscripcion->competidores->sum(function ($competidor) {
                            return $competidor->modalidades->sum('costo');
                        });
                        $totalGeneral = $totalCompetidores + (float) $inscripcion->costo;
                    @endphp
                    <tr>
                        <td style="vertical-align: top;">
                            <strong>{{ $inscripcion->organizacion->nombre }}</strong><br>
                            <small class="text-muted">ID inscripcion: {{ $inscripcion->id }}</small>
                        </td>
                        <td style="text-align: center; vertical-align: top;">
                            <label class="label label-primary">{{ number_format((float) $inscripcion->costo, 2) }}</label>
                        </td>
                        <td style="vertical-align: top;">
                            @forelse ($inscripcion->competidores as $competidor)
                                <div class="mb-2 pb-2 border-bottom">
                                    <strong>{{ $competidor->persona->first_name }}</strong>
                                    <span class="label label-success ms-1">{{ number_format((float) $competidor->total, 2) }}</span>
                                    <ul class="mb-0 mt-1">
                                        @foreach ($competidor->modalidades as $detalle)
                                            <li>
                                                @if ($detalle->categoria)
                                                    {{ $detalle->categoria->nombre }} /
                                                @endif
                                                {{ $detalle->modalidad->nombre }}:
                                                {{ number_format((float) $detalle->costo, 2) }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <span class="text-muted">Sin competidores inscritos</span>
                            @endforelse
                        </td>
                        <td style="text-align: center; vertical-align: top;">
                            <label class="label label-info">{{ number_format((float) $totalCompetidores, 2) }}</label>
                        </td>
                        <td style="text-align: center; vertical-align: top;">
                            <label class="label label-success">{{ number_format((float) $totalGeneral, 2) }}</label>
                        </td>
                        <td style="text-align: center; vertical-align: top;">
                            <button type="button" class="btn btn-sm btn-primary" title="Recibo" data-bs-toggle="modal" data-bs-target="#modal-recibo-organizacion-{{ $inscripcion->id }}">
                                <i class="bi bi-receipt"></i>
                            </button>
                            <a href="{{ route('inscripciones.participantes', [$torneo, $inscripcion]) }}" class="btn btn-sm btn-success" title="Participantes">
                                <i class="bi bi-person-plus"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete-organizacion-{{ $inscripcion->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <h5 class="text-center eventos-empty">
                                <img src="{{ asset('images/empty.png') }}" width="120" alt="Sin resultados">
                                <br><br>
                                No hay inscripciones registradas
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
            @if(count($organizaciones) > 0)
                <p class="text-muted mb-md-0">Mostrando del {{ $organizaciones->firstItem() }} al {{ $organizaciones->lastItem() }} de {{ $organizaciones->total() }} registros.</p>
            @endif
        </div>
        <div class="col-md-8" style="overflow-x:auto">
            <nav class="d-flex justify-content-md-end">
                {{ $organizaciones->links() }}
            </nav>
        </div>
    </div>
</div>

@foreach ($organizaciones as $inscripcion)
    <div class="modal fade" id="modal-delete-organizacion-{{ $inscripcion->id }}" tabindex="-1" aria-labelledby="modalDeleteOrganizacionLabel{{ $inscripcion->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('inscripciones.organizaciones.destroy', [$torneo, $inscripcion]) }}">
                @csrf
                @method('DELETE')

                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="modalDeleteOrganizacionLabel{{ $inscripcion->id }}">Eliminar organizacion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        Seguro que desea eliminar la inscripcion de <strong>{{ $inscripcion->organizacion->nombre }}</strong>?
                        @if ($inscripcion->competidores->isNotEmpty())
                            <div class="alert alert-warning mt-3 mb-0">
                                Tambien se eliminaran sus competidores inscritos en este campeonato.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal-recibo-organizacion-{{ $inscripcion->id }}" tabindex="-1" aria-labelledby="modalReciboOrganizacionLabel{{ $inscripcion->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalReciboOrganizacionLabel{{ $inscripcion->id }}">Recibo de pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="recibo-organizacion-{{ $inscripcion->id }}" class="p-4 border bg-white">
                        <div class="text-center mb-4">
                            <h4 class="mb-1">Recibo de Pago</h4>
                            <div class="fw-bold">Concepto: Inscripcion al campeonato</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="fw-bold">Campeonato</div>
                                <div>{{ $torneo->nombre ?: 'Torneo sin nombre' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold">Fecha</div>
                                <div>{{ now()->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold">Responsable del campeonato</div>
                                <div>{{ $torneo->persona ? $torneo->persona->first_name : 'No registrado' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold">Organizacion</div>
                                <div>{{ $inscripcion->organizacion->nombre }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold">Responsable de la organizacion</div>
                                <div>{{ $inscripcion->organizacion->persona ? $inscripcion->organizacion->persona->first_name : 'No registrado' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold">Monto pagado</div>
                                <div class="fs-5 fw-bold">{{ number_format((float) $inscripcion->costo, 2) }} Bs.</div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-6 text-center">
                                <div style="border-top: 1px solid #000; padding-top: 6px;">Responsable campeonato</div>
                            </div>
                            <div class="col-6 text-center">
                                <div style="border-top: 1px solid #000; padding-top: 6px;">
                                    {{ $inscripcion->organizacion->persona ? $inscripcion->organizacion->persona->first_name : 'No registrado' }}
                                </div>
                                <div>
                                    {{ $inscripcion->organizacion->nombre }}
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="printReciboOrganizacion('recibo-organizacion-{{ $inscripcion->id }}')">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
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

        @if (session('recibo_inscripcion_id'))
            let reciboModal = document.getElementById('modal-recibo-organizacion-{{ session('recibo_inscripcion_id') }}');
            if (reciboModal) {
                new bootstrap.Modal(reciboModal).show();
            }
        @endif
    });

    function printReciboOrganizacion(elementId) {
        let content = document.getElementById(elementId).innerHTML;
        let printWindow = window.open('', '_blank', 'width=900,height=700');

        printWindow.document.write(`
            <html>
                <head>
                    <title>Recibo de pago</title>
                    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body class="p-4">${content}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
</script>
