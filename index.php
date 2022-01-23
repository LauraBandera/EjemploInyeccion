<?php

session_name("examen3_21_22");
session_start();

require "src/ctes_funciones.php";

if(isset($_POST["btnCerrarSesion"]))
{
    session_destroy();
    header("Location:index.php");
    exit;
}

if(isset($_SESSION["usuario"])&& isset($_SESSION["clave"]) && isset($_SESSION["ultimo_acceso"]) )
{
        $salto_baneo_tiempo="index.php";
        require "vistas/vista_seguridad.php";

        /// Si la ejecución sigue por aquí, es porque el usuario está:
        /// 1) Logueado
        /// 2) No ha sido baneado
        /// 3) Y no se le ha expirado el tiempo por inactividad

        if($datos_usuario["tipo"]=="normal")
        {
            require "vistas/vista_normal.php";
            mysqli_close($conexion);
        }   
        else
        {
            mysqli_close($conexion);
            header("Location:admin/gest_libros.php");
            exit;
        }

}
else
{
    require "vistas/vista_login.php";
}
