@extends('layouts.app')

@section('title', 'Datos Personales')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-8 px-3 py-3">
                                <h1 class="h3 mb-0 text-dark">
                                    <i class="bi bi-person"></i> Datos Personales
                                </h1>
                            </div>
                            <div class="col-md-4 text-end px-3 py-3">
                                <a href="#" class="btn btn-success me-2">
                                    <i class="bi bi-plus-lg"></i> <span>Crear</span>
                                </a>
                                <button type="button" class="btn btn-warning text-white" onclick="window.history.back()">
                                    <i class="bi bi-x-lg"></i> <span>Cerrar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-9">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label class="d-flex align-items-center gap-2">Mostrar <select id="select-paginate" class="form-select form-select-sm w-auto">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> registros</label>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <input type="text" id="input-search" placeholder="🔍 Buscar..." class="form-control">
                            </div>
                        </div>
                        <div class="row" id="div-results" style="min-height: 120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-delete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="delete_form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeleteLabel">Eliminar registro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que desea eliminar este registro?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var countPage = 10, order = 'id', typeOrder = 'desc';
        var timeout = null;
        $(document).ready(() => {
            list();
            $('#input-search').on('keyup', function(e){
                if(e.keyCode == 13) {
                    clearTimeout(timeout);
                    list();
                }
            });

            $('#select-paginate').change(function(){
                countPage = $(this).val();
                list();
            });

            $('#input-search').on('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    list();
                }, 2000);
            });
        });

        function list(page = 1){
            $('#div-results').html('<div class="col-12 text-center text-muted py-5">Cargando...</div>');

            let url = '{{ route("people.ajax.list") }}';
            let search = $('#input-search').val() ? $('#input-search').val() : '';

            $.ajax({
                url: `${url}?search=${encodeURIComponent(search)}&paginate=${countPage}&page=${page}`,
                type: 'get',
                success: function(result){
                    $("#div-results").html(result);
                },
                error: function(){
                    $("#div-results").html('<div class="col-12"><div class="alert alert-danger mb-0">No se pudo cargar la lista.</div></div>');
                }
            });
        }
        function deleteItem(url){
            $('#delete_form').attr('action', url);
        }
    </script>
@endpush
