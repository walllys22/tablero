<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover eventos-table">
            <thead>
                <tr>
                    <th style="width: 90px; text-align: center">ID</th>
                    <th style="text-align: center">Nombre</th>
                    <th style="text-align: center">Email</th>
                    <th style="text-align: center">Roles</th>
                    <th style="width: 120px; text-align: center">Estado</th>
                    <th style="width: 180px; text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $imagenUsuario = $item->imagen
                            ? asset('storage/' . ltrim($item->imagen, '/'))
                            : asset('images/icono.png');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $item->id }}</td>
                        <td>
                            <div class="usuario-name-cell">
                                <img src="{{ $imagenUsuario }}" alt="{{ $item->name }}" onerror="this.src='{{ asset('images/icono.png') }}'">
                                <span>{{ $item->name }}</span>
                            </div>
                        </td>
                        <td>{{ $item->email }}</td>
                        <td>
                            @forelse ($item->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted">Sin roles</span>
                            @endforelse
                        </td>
                        <td class="text-center">
                            @if ($item->status == 1)
                                <label class="label label-success">Activo</label>
                            @else
                                <label class="label label-danger">Inactivo</label>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($item->status == 1)
                                <button type="button" title="Inactivar" data-bs-toggle="modal" data-bs-target="#modal-status-user-{{ $item->id }}" class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('usuarios.toggle-status', $item) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Activar" class="btn btn-sm btn-warning text-white">
                                        <i class="bi bi-toggle-off"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" title="Ver" data-bs-toggle="modal" data-bs-target="#modal-view-user-{{ $item->id }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-edit-user-{{ $item->id }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-pencil-square"></i>
                            </button>
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

<style>
    .usuario-name-cell {
        align-items: center;
        display: flex;
        gap: 10px;
    }

    .usuario-name-cell img,
    .usuario-edit-img,
    .usuario-view-img {
        background: #f8f9fa;
        border: 1px solid #e5e7eb;
        border-radius: 50%;
        object-fit: cover;
    }

    .usuario-name-cell img {
        height: 42px;
        width: 42px;
    }

    .usuario-edit-img,
    .usuario-view-img {
        height: 82px;
        width: 82px;
    }
</style>

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
        $imagenUsuario = $item->imagen
            ? asset('storage/' . ltrim($item->imagen, '/'))
            : asset('images/icono.png');
    @endphp
    <div class="modal fade" id="modal-view-user-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Datos del usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3" style="background: #f8f9fa;">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="px-3 py-2 bg-white rounded-3 border text-center h-100">
                                <strong>Imagen</strong><br>
                                <img src="{{ $imagenUsuario }}" alt="{{ $item->name }}" class="usuario-view-img mt-2" onerror="this.src='{{ asset('images/icono.png') }}'">
                            </div>
                        </div>
                        <div class="col-md-4"><div class="px-3 py-2 bg-white rounded-3 border"><strong>ID</strong><br>{{ $item->id }}</div></div>
                        <div class="col-md-4"><div class="px-3 py-2 bg-white rounded-3 border"><strong>Nombre</strong><br>{{ $item->name }}</div></div>
                        <div class="col-md-4"><div class="px-3 py-2 bg-white rounded-3 border"><strong>Email</strong><br>{{ $item->email }}</div></div>
                        <div class="col-md-4"><div class="px-3 py-2 bg-white rounded-3 border"><strong>Estado</strong><br>{{ $item->status == 1 ? 'Activo' : 'Inactivo' }}</div></div>
                        <div class="col-md-12">
                            <div class="px-3 py-2 bg-white rounded-3 border">
                                <strong>Roles</strong><br>
                                @forelse ($item->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">Sin roles</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-status-user-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form method="POST" action="{{ route('usuarios.toggle-status', $item) }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark fw-bold">
                        <h5 class="modal-title">Alerta</h5>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">Esta seguro de cambiar el estado?</div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-danger">Si</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-user-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('usuarios.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="editing_usuario" value="{{ $item->id }}">
                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title fw-bold">Editar usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name_edit_{{ $item->id }}" class="form-label">Nombre</label>
                                <input type="text" name="name" id="name_edit_{{ $item->id }}" value="{{ old('editing_usuario') == $item->id ? old('name') : $item->name }}" class="form-control @if(old('editing_usuario') == $item->id) @error('name') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('editing_usuario') == $item->id) @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="email_edit_{{ $item->id }}" class="form-label">Email</label>
                                <input type="email" name="email" id="email_edit_{{ $item->id }}" value="{{ old('editing_usuario') == $item->id ? old('email') : $item->email }}" class="form-control @if(old('editing_usuario') == $item->id) @error('email') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('editing_usuario') == $item->id) @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-9">
                                <label for="imagen_edit_{{ $item->id }}" class="form-label">Imagen</label>
                                <input type="file" name="imagen" id="imagen_edit_{{ $item->id }}" class="form-control js-user-image-input @if(old('editing_usuario') == $item->id) @error('imagen') is-invalid @enderror @endif" accept="image/jpeg,image/png,image/webp" data-preview="usuario_preview_edit_{{ $item->id }}">
                                @if (old('editing_usuario') == $item->id) @error('imagen')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-3 text-center">
                                <img src="{{ $imagenUsuario }}" alt="{{ $item->name }}" id="usuario_preview_edit_{{ $item->id }}" class="usuario-edit-img" onerror="this.src='{{ asset('images/icono.png') }}'">
                            </div>
                            <div class="col-md-6">
                                <label for="password_edit_{{ $item->id }}" class="form-label">Nueva contrasena</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password_edit_{{ $item->id }}" class="form-control @if(old('editing_usuario') == $item->id) @error('password') is-invalid @enderror @endif" autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary js-toggle-password" data-target="password_edit_{{ $item->id }}" aria-label="Ver contrasena">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @if (old('editing_usuario') == $item->id) @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation_edit_{{ $item->id }}" class="form-label">Confirmar nueva contrasena</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation_edit_{{ $item->id }}" class="form-control" autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary js-toggle-password" data-target="password_confirmation_edit_{{ $item->id }}" aria-label="Ver contrasena">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Roles</label>
                                @php
                                    $selectedRoles = old('editing_usuario') == $item->id ? old('roles', []) : $item->roles->pluck('id')->all();
                                @endphp
                                <div class="row g-2">
                                    @foreach ($roles as $role)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="roles[]" id="role_edit_{{ $item->id }}_{{ $role->id }}" value="{{ $role->id }}" class="form-check-input" {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }}>
                                                <label for="role_edit_{{ $item->id }}_{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="status" id="status_edit_{{ $item->id }}" value="1" class="form-check-input" {{ old('editing_usuario') == $item->id ? (old('status') ? 'checked' : '') : ($item->status == 1 ? 'checked' : '') }}>
                                    <label for="status_edit_{{ $item->id }}" class="form-check-label">Activo</label>
                                </div>
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

        @if ($errors->any() && old('editing_usuario'))
            let editModal = document.getElementById('modal-edit-user-{{ old('editing_usuario') }}');
            if (editModal) {
                new bootstrap.Modal(editModal).show();
            }
        @endif
    });

    document.addEventListener('change', function (event) {
        if (!event.target.classList.contains('js-user-image-input')) {
            return;
        }

        const preview = document.getElementById(event.target.dataset.preview);
        const file = event.target.files && event.target.files[0];

        if (!preview || !file) {
            return;
        }

        preview.src = URL.createObjectURL(file);
    });
</script>
