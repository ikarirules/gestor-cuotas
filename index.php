<?php
// La app real vive en frontend/web (usuarios) y backend/web (admin).
// Esto evita que se vea el listado de carpetas al entrar a la raiz del proyecto.
header('Location: frontend/web/');
exit;
