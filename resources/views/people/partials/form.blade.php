@php
    $editingId = $editing && $persona ? $persona->id : null;
    $useOld = !$editing || old('editing_persona') == $editingId;
    $fieldId = $editing ? '_edit_' . $editingId : '';

    $value = function (string $field, $default = null) use ($persona, $useOld) {
        if ($useOld) {
            return old($field, $persona?->{$field} ?? $default);
        }

        return $persona?->{$field} ?? $default;
    };

    $dateValue = function () use ($persona, $useOld) {
        if ($useOld) {
            return old('birth_date', optional($persona?->birth_date)->format('Y-m-d'));
        }

        return optional($persona?->birth_date)->format('Y-m-d');
    };

    $hasError = function (string $field) use ($errors, $useOld) {
        return $useOld && $errors->has($field);
    };
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label for="ci{{ $fieldId }}" class="form-label">Documento</label>
        <input type="text" name="ci" id="ci{{ $fieldId }}" value="{{ $value('ci') }}" class="form-control {{ $hasError('ci') ? 'is-invalid' : '' }}" maxlength="255">
        @if ($hasError('ci'))
            <div class="invalid-feedback">{{ $errors->first('ci') }}</div>
        @endif
    </div>

    <div class="col-md-8">
        <label for="first_name{{ $fieldId }}" class="form-label">Nombre completo</label>
        <input type="text" name="first_name" id="first_name{{ $fieldId }}" value="{{ $value('first_name') }}" class="form-control {{ $hasError('first_name') ? 'is-invalid' : '' }}" maxlength="255" required>
        @if ($hasError('first_name'))
            <div class="invalid-feedback">{{ $errors->first('first_name') }}</div>
        @endif
    </div>

    <div class="col-md-4">
        <label for="birth_date{{ $fieldId }}" class="form-label">Fecha nacimiento</label>
        <input type="date" name="birth_date" id="birth_date{{ $fieldId }}" value="{{ $dateValue() }}" class="form-control {{ $hasError('birth_date') ? 'is-invalid' : '' }}">
        @if ($hasError('birth_date'))
            <div class="invalid-feedback">{{ $errors->first('birth_date') }}</div>
        @endif
    </div>

    <div class="col-md-4">
        <label for="gender{{ $fieldId }}" class="form-label">Genero</label>
        <select name="gender" id="gender{{ $fieldId }}" class="form-select {{ $hasError('gender') ? 'is-invalid' : '' }}">
            <option value="">Seleccione</option>
            <option value="Masculino" {{ $value('gender') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
            <option value="Femenino" {{ $value('gender') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
        </select>
        @if ($hasError('gender'))
            <div class="invalid-feedback">{{ $errors->first('gender') }}</div>
        @endif
    </div>

    <div class="col-md-4">
        <label for="sangre{{ $fieldId }}" class="form-label">Tipo sangre</label>
        <input type="text" name="sangre" id="sangre{{ $fieldId }}" value="{{ $value('sangre') }}" class="form-control {{ $hasError('sangre') ? 'is-invalid' : '' }}" maxlength="20">
        @if ($hasError('sangre'))
            <div class="invalid-feedback">{{ $errors->first('sangre') }}</div>
        @endif
    </div>

    <div class="col-md-6">
        <label for="email{{ $fieldId }}" class="form-label">Email</label>
        <input type="email" name="email" id="email{{ $fieldId }}" value="{{ $value('email') }}" class="form-control {{ $hasError('email') ? 'is-invalid' : '' }}" maxlength="255">
        @if ($hasError('email'))
            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
        @endif
    </div>

    <div class="col-md-2">
        <label for="country_code{{ $fieldId }}" class="form-label">Codigo</label>
        <input type="text" name="country_code" id="country_code{{ $fieldId }}" value="{{ $value('country_code', '591') }}" class="form-control {{ $hasError('country_code') ? 'is-invalid' : '' }}" maxlength="10">
        @if ($hasError('country_code'))
            <div class="invalid-feedback">{{ $errors->first('country_code') }}</div>
        @endif
    </div>

    <div class="col-md-4">
        <label for="phone{{ $fieldId }}" class="form-label">Telefono</label>
        <input type="text" name="phone" id="phone{{ $fieldId }}" value="{{ $value('phone') }}" class="form-control {{ $hasError('phone') ? 'is-invalid' : '' }}" maxlength="50">
        @if ($hasError('phone'))
            <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
        @endif
    </div>

    <div class="col-md-8">
        <label for="address{{ $fieldId }}" class="form-label">Direccion</label>
        <input type="text" name="address" id="address{{ $fieldId }}" value="{{ $value('address') }}" class="form-control {{ $hasError('address') ? 'is-invalid' : '' }}" maxlength="1000">
        @if ($hasError('address'))
            <div class="invalid-feedback">{{ $errors->first('address') }}</div>
        @endif
    </div>

    <div class="col-md-4">
        <label for="image{{ $fieldId }}" class="form-label">Imagen</label>
        <input type="file" name="image" id="image{{ $fieldId }}" class="form-control {{ $hasError('image') ? 'is-invalid' : '' }}" accept="image/jpeg,image/png,image/webp">
        @if ($persona?->image)
            <small class="text-muted">Imagen actual: {{ basename($persona->image) }}</small>
        @endif
        @if ($hasError('image'))
            <div class="invalid-feedback">{{ $errors->first('image') }}</div>
        @endif
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="checkbox" name="status" id="status{{ $fieldId }}" value="1" class="form-check-input" {{ $value('status', 1) ? 'checked' : '' }}>
            <label for="status{{ $fieldId }}" class="form-check-label">Activo</label>
        </div>
    </div>
</div>
