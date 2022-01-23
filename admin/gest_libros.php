<?php
session_name("examen3_21_22");
session_start();

require "../src/ctes_funciones.php";


if(isset($_SESSION["usuario"])&& isset($_SESSION["clave"]) && isset($_SESSION["ultimo_acceso"]) )
{
        $salto_baneo_tiempo="../index.php";
        require "../vistas/vista_seguridad.php";
        
        /// Si la ejecución sigue por aquí, es porque el usuario está:
        /// 1) Logueado
        /// 2) No ha sido baneado
        /// 3) Y no se le ha expirado el tiempo por inactividad


        if($datos_usuario["tipo"]=="admin")
        {
            require "../vistas/vista_admin.php";
            mysqli_close($conexion);
        }
        else
        {
            mysqli_close($conexion);
            header("Location:../index.php");
            exit;
        }
    
}
else
{
    $_SESSION["restringida"]="* Está usted accediendo a una zona restringida. Por favor, vuelva a loguearse. *";
    header("Location:../index.php");
    exit;
}
?>