
<?php
if(isset($_POST["btnLogin"]))
{
    $error_usuario=$_POST["usuario"]=="";
    $error_clave=$_POST["clave"]=="";
    $error_form_login=$error_usuario||$error_clave;
    if(!$error_form_login)
    {
        @$conexion=mysqli_connect(SERVIDOR_BD,USUARIO_BD,CLAVE_BD,NOMBRE_BD);
        if(!$conexion)
        {
            session_destroy();
            die(error_page("Librería","<h1>Librería</h1><p>Error en la conexión Nº: ".mysqli_connect_errno(). " : ".mysqli_connect_error()."</p>"));
        }
        mysqli_set_charset($conexion,"utf8");

        //Modificación para evitar la inyección en sql
        $consulta="select * from usuarios where lector=? and clave=?";
        $sentencia = mysqli_stmt_init($conexion);
        if($sentencia = mysqli_prepare($conexion, $consulta)){
            $clave_encriptada = md5($_POST["clave"]);
            //Entre las comillas ponemos ss para hacer referencia a dos string
            mysqli_stmt_bind_param($sentencia, 'ss', $_POST["usuario"], $clave_encriptada);
            if(mysqli_stmt_execute($sentencia))
            {
                $resultado=mysqli_stmt_get_result($sentencia);
                if($datos=mysqli_fetch_assoc($resultado))
                {
                    $_SESSION["usuario"]=$_POST["usuario"];
                    $_SESSION["clave"]=$clave_encriptada;
                    $_SESSION["ultimo_acceso"]=time();
                    mysqli_free_result($resultado);
                    mysqli_close($conexion);
                    if($datos["tipo"]=="normal")
                        header("Location:index.php");
                    else
                        header("Location:admin/gest_libros.php");
                    exit;
                }
                else
                {
                    $error_usuario=true;
                }
            }
            else
            {
                $body="<h1>Librería</h1><p>Error en la consulta Nº: ".mysqli_errno($conexion). " : ".mysqli_error($conexion)."</p>";
                mysqli_close($conexion);
                session_destroy();
                die(error_page("Librería",$body));
            }
        }else{
            $body="<h1>Librería</h1><p>Error: No se ha podido preparar la consulta: ".$consulta."</p>";
            mysqli_close($conexion);
            session_destroy();
            die(error_page("Librería",$body));
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librería</title>
    <style>
        .error{color:red}
        .contenedor{width:100%}
        .contenedor div{width:30%;float:left; text-align: center;margin-top: 2.5%;margin-left:2.5%;}
        .contenedor div img{width:80%;}
    </style>
</head>
<body>
    <h1>Librería</h1>
    <?php
    if(isset($_SESSION["restringida"]))
    {
        echo "<p class='error'>".$_SESSION["restringida"]."</p>";
        session_destroy();
    }
    if(isset($_SESSION["tiempo"]))
    {
        echo "<p class='error'>".$_SESSION["tiempo"]."</p>";
        session_destroy();
    }
    ?>
    <form action="index.php" method="post">
        <p>
            <label for="usuario">Usuario: </label>
            <input type="text" id="usuario" name="usuario" value="<?php if(isset($_POST["usuario"])) echo $_POST["usuario"]; ?>"/>
            <?php
            if(isset($_POST["btnLogin"])&& $error_usuario)
                if($_POST["usuario"]=="")
                    echo "<span class='error'> * Campo Vacío * </span>";
                else
                    echo "<span class='error'> * Usuario/Contraseña no registrado en la BD * </span>";
            ?>
        </p>
        <p>
            <label for="clave">Contraseña: </label>
            <input type="password" id="clave" name="clave" />
            <?php
            if(isset($_POST["btnLogin"])&& $error_clave)
                echo "<span class='error'> * Campo Vacío * </span>";
            ?>
        </p>
        <p>
            <input type="submit" name="btnLogin" value="Login"/> 
        </p>
    </form>

    <?php
        @$conexion=mysqli_connect(SERVIDOR_BD,USUARIO_BD,CLAVE_BD,NOMBRE_BD);
        if(!$conexion)
        {
            session_destroy();
            die("<p>Error en la conexión Nº: ".mysqli_connect_errno(). " : ".mysqli_connect_error()."</p></body></html>");
        }
        mysqli_set_charset($conexion,"utf8");

        require "vistas/vista_libros_tres_en_tres.php";
            
        mysqli_close($conexion);
        
    ?>
</body>
</html>
