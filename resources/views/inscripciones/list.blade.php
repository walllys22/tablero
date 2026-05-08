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
                                                @if ($detalle->modalidad->categoria)
                                                    {{ $detalle->modalidad->categoria->nombre }} /
                                                @endif
                                                {{ $detalle->modalidad->nombre }} - {{ $detalle->modalidad->genero }}:
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
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
