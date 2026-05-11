<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if ($method)
                @method($method)
            @endif
            <div class="modal-content"> 
                <div class="modal-header bg-{{ $color }} text-white">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Persona</label>
                            <select name="persona_id" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach ($personas as $persona)
                                    <option value="{{ $persona->id }}" {{ old('persona_id', $arbitro?->persona_id) == $persona->id ? 'selected' : '' }}>
                                        {{ $persona->first_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cargo</label>
                            <select name="cargo" class="form-select" required>
                                @foreach (['Juez', 'Referee'] as $cargo)
                                    <option value="{{ $cargo }}" {{ old('cargo', $arbitro?->cargo) === $cargo ? 'selected' : '' }}>{{ $cargo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Modalidad</label>
                            <select name="modalidad" class="form-select" required>
                                @foreach (['Kata', 'Kumite', 'Kumite-Kata'] as $modalidad)
                                    <option value="{{ $modalidad }}" {{ old('modalidad', $arbitro?->modalidad) === $modalidad ? 'selected' : '' }}>{{ $modalidad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Rango</label>
                            <select name="rango" class="form-select" required>
                                @foreach (['A', 'B', 'C'] as $rango)
                                    <option value="{{ $rango }}" {{ old('rango', $arbitro?->rango) === $rango ? 'selected' : '' }}>{{ $rango }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Tipo de licencia</label>
                            <select name="licencia_tipo_id" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach ($licencias as $licencia)
                                    <option value="{{ $licencia->id }}" {{ old('licencia_tipo_id', $arbitro?->licencia_tipo_id) == $licencia->id ? 'selected' : '' }}>
                                        {{ $licencia->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
