@php
    $formatCategoriaNombre = function ($nombre) {
        return str_replace(
            ["a\xC3\x83\xC6\x92\xC3\x82\xC2\xB1os", "a\xC3\x83\xC2\xB1os", 'anos', 'menor o igual', 'mayor o igual'],
            ["a\u{00F1}os", "a\u{00F1}os", "a\u{00F1}os", "\u{2264}", "\u{2265}"],
            $nombre
        );
    };
@endphp

<style>
    .modalidad-toggle {
        align-items: center;
        background: transparent;
        border: 0;
        color: #343a40;
        display: flex;
        font: inherit;
        font-weight: 700;
        gap: 7px;
        padding: 0;
        text-align: left;
        width: 100%;
    }

    .modalidad-toggle .bi-chevron-down {
        transition: transform .15s ease-in-out;
    }

    .modalidad-toggle.collapsed .bi-chevron-down {
        transform: rotate(-90deg);
    }

    .categoria-collapse {
        min-height: 0;
    }
</style>

<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="text-align: center">Modalidad</th>
                    <th style="text-align: center">Categorias</th>
                    <th style="text-align: center">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td style="vertical-align: middle;">
                            <button
                                type="button"
                                class="modalidad-toggle collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#categorias-modalidad-{{ $item->id }}"
                                aria-expanded="false"
                                aria-controls="categorias-modalidad-{{ $item->id }}"
                            >
                                <i class="bi bi-chevron-down"></i>
                                <span>{{ $item->nombre }}</span>
                            </button>
                        </td>

                        <td style="vertical-align: middle;">
                            <div class="collapse categoria-collapse" id="categorias-modalidad-{{ $item->id }}">
                                @forelse ($item->categorias as $categoria)
                                    <div>
                                        <strong>{{ $formatCategoriaNombre($categoria->nombre) }}</strong>
                                    </div>
                                @empty
                                    <span class="text-muted">Sin categorias</span>
                                @endforelse
                            </div>
                        </td>
                        <td style="vertical-align: middle; width: 14%" class="text-end p-2">
                            <div class="d-flex flex-wrap justify-content-end gap-2">
                                <a href="{{ route('modalidades.show', ['torneo' => $torneo, 'modalidad' => $item, 'return' => request('return')]) }}" title="Ver" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-edit-modalidad-{{ $item->id }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete-modalidad-{{ $item->id }}" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button type="button" title="Categoria" data-bs-toggle="modal" data-bs-target="#modal-create-categoria" data-modalidad-id="{{ $item->id }}" data-modalidad-nombre="{{ $item->nombre }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-tags"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
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
    <div class="modal fade" id="modal-edit-modalidad-{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditModalidadLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" action="{{ route('modalidades.update', ['torneo' => $torneo, 'modalidad' => $item, 'return' => request('return')]) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_modalidad" value="{{ $item->id }}">

                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold" id="modalEditModalidadLabel{{ $item->id }}">Editar modalidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nombre_modalidad_{{ $item->id }}" class="form-label">Modalidad</label>
                                <input type="text" name="nombre" id="nombre_modalidad_{{ $item->id }}" value="{{ old('editing_modalidad') == $item->id ? old('nombre') : $item->nombre }}" class="form-control @if(old('editing_modalidad') == $item->id) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('editing_modalidad') == $item->id)
                                    @error('nombre')
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

    <div class="modal fade" id="modal-delete-modalidad-{{ $item->id }}" tabindex="-1" aria-labelledby="modalDeleteModalidadLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('modalidades.destroy', ['torneo' => $torneo, 'modalidad' => $item, 'return' => request('return')]) }}">
                @csrf
                @method('DELETE')

                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalDeleteModalidadLabel{{ $item->id }}">Eliminar modalidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        Seguro que desea eliminar la modalidad <strong>{{ $item->nombre }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
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

        @if ($errors->any() && old('editing_modalidad'))
            let editModal = document.getElementById('modal-edit-modalidad-{{ old('editing_modalidad') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });
</script>
