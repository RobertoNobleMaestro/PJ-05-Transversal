<?php
// Este es un archivo temporal para configurar las claves de prueba de Stripe
// Deberás reemplazar estas claves con tus propias claves de Stripe

// Claves de prueba de Stripe (públicas)
$stripePublishableKey = 'pk_test_51O1vHISJdB60CBNJQiAvqhVGGSakBnOWx9xSrGHMIzGvQQcYrIpRzA10Wl26XVwGYQoodBXEfFw2UHbqCfnnq4Wm00OejStbDO';
$stripeSecretKey = 'sk_test_51O1vHISJdB60CBNJIcbOeXHzBjBEVB1zY6UQsQBCYA7E5GPKtoqA8ZA6bz7Nf1k4WbnOAJtqCLNVQbWJrTCPkXJC00EAyiDqhJ';

// Añadir las claves al archivo .env
$envFile = __DIR__ . '/.env';
$envContent = file_get_contents($envFile);

// Verificar si las claves de Stripe ya existen
if (!strpos($envContent, 'STRIPE_KEY=')) {
    $envContent .= "\n\nSTRIPE_KEY={$stripePublishableKey}";
}
if (!strpos($envContent, 'STRIPE_SECRET=')) {
    $envContent .= "\nSTRIPE_SECRET={$stripeSecretKey}";
}

// Guardar el archivo .env actualizado
file_put_contents($envFile, $envContent);

echo "Claves de Stripe configuradas correctamente. Recuerda reemplazar estas claves con tus propias claves de producción en el archivo .env.\n";
?>
