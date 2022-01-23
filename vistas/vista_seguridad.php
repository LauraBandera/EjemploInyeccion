<?php
@$conexion=mysqli_connect(SERVIDOR_BD,USUARIO_BD,CLAVE_BD,NOMBRE_BD);
if(!$conexion)
{
    session_destroy();
    die(error_page("Librería","<h1>Librería</h1><p>Error en la conexión Nº: ".mysqli_connect_errno(). " : ".mysqli_connect_error()."</p>"));
}
    
mysqli_set_charset($conexion,"utf8");

$consulta="select * from usuarios where lector='".$_SESSION["usuario"]."' and clave='".$_SESSION["clave"]."'";
$resultado=mysqli_query($conexion,$consulta);
if($resultado)
{
    if($datos_usuario=mysqli_fetch_assoc($resultado))//Compruebo que no está baneado
    {
        mysqli_free_result($resultado);

        if(time()-$_SESSION["ultimo_acceso"]>INACTIVIDAD*60)//Compruebo que no le ha pasado el tiempo de inactividad
        {
            session_unset();
            $_SESSION["tiempo"]="* Su tiempo de sesión ha caducado. Por favor, vuelva a loguearse. *";
            mysqli_close($conexion);
            header("Location:".$salto_baneo_tiempo);
            exit;
        }
        
    }
    else
    {
        session_unset();
        $_SESSION["restringida"]="* Está usted accediendo a una zona restringida. Por favor, vuelva a loguearse. *";
        mysqli_free_result($resultado);
        mysqli_close($conexion);
        header("Location:".$salto_baneo_tiempo);
        exit;
    }
}
else
{
    $body="<h1>Librería</h1><p>Error en la consulta Nº: ".mysqli_errno($conexion). " : ".mysqli_error($conexion)."</p>";
    mysqli_close($conexion);
    session_destroy();
    die(error_page("Librería",$body));
}


$_SESSION["ultimo_acceso"]=time(); //Renuevo el tiempo
?>