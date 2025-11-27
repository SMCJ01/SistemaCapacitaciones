<?php

// ===========================================
// FUNCIÓN PARA FORMATEAR PRECIOS
// ===========================================
function formatearPrecio($precio)
{
    return "S/ " . number_format($precio, 2);
}

// ===========================================
// FUNCIÓN PARA OBTENER IMAGEN DE CURSO (TEMPORAL)
// LUEGO AÑADIRÉMOS LAS 700 URLS SIN REPETIR
// ===========================================
function obtenerImagenCurso($titulo)
{
    return "https://picsum.photos/seed/" . urlencode($titulo) . "/600/400";
}

?>
