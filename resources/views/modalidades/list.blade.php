<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="text-align: center">ID</th>
                    <th style="text-align: center">Categoria</th>
                    <th style="text-align: center">Modalidad</th>
                    <th style="text-align: center">Genero</th>
                    <th style="text-align: center">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">
                            {{ $item->id }}
                        </td>
                        <td style="vertical-align: middle;">
                            @if ($item->categoria)
                                <strong>{{ $item->categoria->nombre }}</strong><br>
                                <small class="text-muted">{{ $item->categoria->descripcion ?: 'Sin detalle' }}</small>
                            @else
                                <span class="text-muted">Sin categoria</span>
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            <strong>{{ $item->nombre }}</strong>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <label class="label {{ strtolower($item->genero) === 'masculino' ? 'label-primary' : 'label-danger' }}">
                                {{ $item->genero }}
                            </label>
                        </td>
                        <td style="vertical-align: middle; width: 14%" class="text-end">
                            <button type="button" title="Ver" data-bs-toggle="modal" data-bs-target="#modal-view-modalidad-{{ $item->id }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-edit-modalidad-{{ $item->id }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modal-delete-modalidad-{{ $item->id }}" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
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
    <div class="modal fade" id="modal-view-modalidad-{{ $item->id }}" tabindex="-1" aria-labelledby="modalViewModalidadLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewModalidadLabel{{ $item->id }}">Ver modalidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">ID</label>
                            <input type="text" value="{{ $item->id }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Torneo</label>
                            <input type="text" value="{{ $torneo->nombre ?: 'Torneo sin nombre' }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Categoria</label>
                            <input type="text" value="{{ $item->categoria ? $item->categoria->nombre : 'Sin categoria' }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Detalle categoria</label>
                            <input type="text" value="{{ $item->categoria ? ($item->categoria->descripcion ?: 'Sin detalle') : 'Sin detalle' }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Modalidad</label>
                            <input type="text" value="{{ $item->nombre }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Genero</label>
                            <input type="text" value="{{ $item->genero }}" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-modalidad-{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditModalidadLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" action="{{ route('modalidades.update', ['torneo' => $torneo, 'modalidad' => $item, 'return' => request('return')]) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_modalidad" value="{{ $item->id }}">

                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold" id="modalEditModalidadLabel{{ $item->id }}">Editar modalidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="categoria_modalidad_{{ $item->id }}" class="form-label">Categoria</label>
                                <select name="categoria_id" id="categoria_modalidad_{{ $item->id }}" class="form-select @if(old('editing_modalidad') == $item->id) @error('categoria_id') is-invalid @enderror @endif" required>
                                    <option value="">Seleccione</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ (old('editing_modalidad') == $item->id ? old('categoria_id') : $item->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}{{ $categoria->descripcion ? ' | ' . $categoria->descripcion : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (old('editing_modalidad') == $item->id)
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-8">
                                <label for="nombre_modalidad_{{ $item->id }}" class="form-label">Modalidad</label>
                                <input type="text" name="nombre" id="nombre_modalidad_{{ $item->id }}" value="{{ old('editing_modalidad') == $item->id ? old('nombre') : $item->nombre }}" class="form-control @if(old('editing_modalidad') == $item->id) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('editing_modalidad') == $item->id)
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="genero_modalidad_{{ $item->id }}" class="form-label">Genero</label>
                                <select name="genero" id="genero_modalidad_{{ $item->id }}" class="form-select @error('genero') is-invalid @enderror" required>
                                    <option value="Masculino" {{ (old('editing_modalidad') == $item->id ? old('genero') : $item->genero) === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ (old('editing_modalidad') == $item->id ? old('genero') : $item->genero) === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                </select>
                                @if (old('editing_modalidad') == $item->id)
                                    @error('genero')
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
                    <div class="modal-header">
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
