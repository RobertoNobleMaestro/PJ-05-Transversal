@extends('layouts.admin')

@section('title', 'CRUD de Usuarios')

@section('content')
    <div class="container mt-5">
        <h1>Gestión de Usuarios</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id_usuario }}</td>
                    <td>{{ $user->nombre }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        {{ $user->nombre_rol ?? 'Sin rol asignado' }}
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="window.location.href='{{ route('admin.users.edit', ['id_usuario' => $user->id_usuario]) }}'">Editar</button>
                        <form action="{{ route('admin.users.destroy', ['id_usuario' => $user->id_usuario]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Añadir Usuario</a>
    </div>
@endsection

<script>
function deleteUser(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload(); // Recargar la página para actualizar la lista de usuarios
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}
</script>
