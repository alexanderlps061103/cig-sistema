<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Selecciona tu perfil</h2>
        <p>Has iniciado sesión como <strong>{{ Auth::user()->nombres }} {{ Auth::user()->apellidos }}</strong></p>
        <form method="POST" action="{{ route('set-role') }}">
            @csrf
            @foreach($roles as $rol)
                <button type="submit" name="rol" value="{{ $rol->nombre }}" class="btn btn-primary btn-lg m-2">
                    {{ ucfirst($rol->nombre) }}
                </button>
            @endforeach
        </form>
    </div>
</body>
</html>
