<?php
session_start();
session_unset();
session_destroy();

header("Location: /SistemaCapacitaciones/inicio/index.php");
exit;
