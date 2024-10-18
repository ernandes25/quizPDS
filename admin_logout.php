<?php
session_start();
session_destroy(); // Destrói todas as sessões
header('Location: admin_login.html'); // Redireciona para a página de login após o logout
exit();
?>
