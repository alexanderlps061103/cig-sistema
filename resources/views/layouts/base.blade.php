<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Gran Sitio Web</title>
</head>
<body>
    <nav>
        @yield('menu')
    </nav>
    <div class="container">
        @yield('contenido')
    </div>
    <footer>
        <p>&copy; 2024 Mi Gran Sitio Web</p>
    </footer>
</body>
</html>