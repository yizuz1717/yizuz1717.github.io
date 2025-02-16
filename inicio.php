<?php
// URL de la API de Turso (reemplaza con la URL correcta de tu base de datos)
$api_url = 'https://sesiones1.turso.io';

// Token de autenticación (reemplaza con tu token)
$api_token = 'eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3Mzk2NjEzMzUsImlkIjoiNjgyNTEwNDItZjQ3Yy00ODU5LWExYmUtYjkyOTY3NzY0NzNlIn0.MGjROzZzltW1CSN0PrW68YRtvapHkO5AWF1y0rglnEWi2MkyJs0WXEYUeD9ovwvvnIM1zK6rqxroAt0UClytBQ';

// Recibir datos del formulario
$nombre = $_POST['txtusuario'];
$pass = $_POST['txtpassword'];

// Consulta SQL con placeholders para evitar inyecciones SQL
$sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";

// Crear la solicitud cURL
$ch = curl_init("$api_url/v2/query"); // Usar el endpoint correcto
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: Bearer $api_token"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'statements' => [
        [
            'q' => $sql,
            'params' => [$nombre]
        ]
    ]
]));

// Ejecutar la solicitud
$response = curl_exec($ch);

// Verificar errores de cURL
if (curl_errno($ch)) {
    die('Error de conexión: ' . curl_error($ch));
}
curl_close($ch);

// Procesar la respuesta
$result = json_decode($response, true);

// Verificar si la respuesta contiene resultados
if (isset($result['results'][0]['rows']) && count($result['results'][0]['rows']) > 0) {
    // Obtener el hash de la contraseña almacenada en la base de datos
    $stored_hash = $result['results'][0]['rows'][0]['contrasena'];

    // Verificar la contraseña
    if (password_verify($pass, $stored_hash)) {
        // Usuario autenticado correctamente
        header('Location: menu.html');
        exit;
    } else {
        // Contraseña incorrecta
        echo "<script>alert('Usuario o contraseña incorrectos'); window.location='index.html';</script>";
        exit;
    }
} else {
    // Usuario no encontrado
    echo "<script>alert('Usuario o contraseña incorrectos'); window.location='index.html';</script>";
    exit;
}
?>