<?php
if(isset($_POST["btnEditar"]))
{
    $_SESSION["accion"]="El libro con referencia ".$_POST["btnEditar"]." ha sido editado con éxito";
    mysqli_close($conexion);
    header("Location:gest_libros.php");
    exit();
}

if(isset($_POST["btnBorrar"]))
{
    $_SESSION["accion"]="El libro con referencia ".$_POST["btnBorrar"]." ha sido borrado con éxito";
    mysqli_close($conexion);
    header("Location:gest_libros.php");
    exit();
}


if(isset($_POST["btnAgregar"]))
{
    
    $error_referencia=$_POST["referencia"]==""|| !is_numeric($_POST["referencia"])|| $_POST["referencia"]<=0;

    if(!$error_referencia)
    {

        $error_referencia=repetido($conexion,"libros","referencia",$_POST["referencia"]);
        if(is_array($error_referencia))
        {
            mysqli_close($conexion);
            session_destroy();
            die(error_page("Librería","<p>".$error_referencia["error"]."</p>"));
        }
    }
    
    $error_titulo=$_POST["titulo"]=="";
    $error_autor=$_POST["autor"]=="";
    $error_descripcion=$_POST["descripcion"]=="";
    $error_precio=$_POST["precio"]==""|| !is_numeric($_POST["precio"])|| $_POST["precio"]<=0;

    $error_foto=$_FILES["foto"]["name"]!="" && ( $_FILES["foto"]["error"] || !getimagesize($_FILES["foto"]["tmp_name"])|| $_FILES["foto"]["size"]>750*1000 );

    $errores_form_nuevo=$error_referencia||$error_titulo||$error_autor||$error_descripcion||$error_precio||$error_foto;
    
    if(!$errores_form_nuevo)
    {
        //Evitamos inyección en cualquier otra consulta que no sea un select
        $consulta="insert into libros (referencia,titulo,autor,descripcion,precio) values (?,?,?,?,?)";
        $sentencia = mysqli_stmt_init($conexion);
        if($sentencia = mysqli_prepare($conexion, $consulta)){
            mysqli_stmt_bind_param($sentencia, 'isssd', $_POST["referencia"], $_POST["titulo"], $_POST["autor"], $_POST["descripcion"], $_POST["precio"]);
            if(mysqli_stmt_execute($sentencia))
            {
                $_SESSION["accion"]="Libro insertado con éxito";
                if($_FILES["foto"]["name"]!="")
                {
                    $ult_id=$_POST["referencia"];

                    $array_aux=explode(".",$_FILES["foto"]["name"]);
                    if(count($array_aux)==1)
                        $extension="";
                    else
                        $extension=".".end($array_aux);

                    $nombre_img="img".$ult_id.$extension;
                    @$var=move_uploaded_file($_FILES["foto"]["tmp_name"],"../Images/".$nombre_img);
                    if($var)
                    {
                        $consulta="update libros set portada='".$nombre_img."' where referencia=".$ult_id;
                        $resultado=mysqli_query($conexion,$consulta);
                        if(!$resultado)
                        {
                            unlink("../Images/".$nombre_img);
                            $body="<h1>Librería</h1><p>Error en la consulta Nº: ".mysqli_errno($conexion). " : ".mysqli_error($conexion)."</p>";
                            mysqli_close($conexion);
                            session_destroy();
                            die(error_page("Librería",$body));
                        }
            
                    }
                    else
                        $_SESSION["accion"]="Libro insertado con la portada por defecto, debido a que no ha podido moverse a la carpeta destino";			
                }

                mysqli_close($conexion);
                header("Location:gest_libros.php");
                exit;
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
        .enlinea{display:inline}
        .sin_boton{background-color: transparent;border:none;color:blue;text-decoration: underline;cursor:pointer}
        #t_libros{border-collapse:collapse;width:80%;margin:0 auto;text-align:center}
        #t_libros, #t_libros td, #t_libros th{border:1px solid black;}
        #t_libros th{background-color: #CCC;}
    </style>
</head>
<body>
    <h1>Librería</h1>
    <div>Bienvenido <strong><?php echo $_SESSION["usuario"];?></strong> - 
        <form class="enlinea" method="post" action="../index.php">
            <button class="sin_boton" type="submit" name="btnCerrarSesion">Salir</button>
        </form>
    </div>
    <?php
        if(isset($_SESSION["accion"]))
        {
            echo "<p class='mensaje'>".$_SESSION["accion"]."</p>";
            unset($_SESSION["accion"]);
        }
   
        require "../vistas/vista_libros_tabla.php";
        ?>
    
    <h3>Agregar un nuevo Libro</h3>
    <form action="gest_libros.php" method="post" enctype="multipart/form-data">
        <table>
        <tr>
            <td><label for="referencia">Referencia: </label></td>
            <td><input type="text" id="referencia" name="referencia" value="<?php if(isset($_POST["referencia"])) echo $_POST["referencia"]; ?>"/></td>
            <?php
            if(isset($_POST["btnAgregar"])&& $error_referencia)
            {
                echo "<td>";
                if($_POST["referencia"]=="")
                    echo "<span class='error'> * Campo Vacío * </span>";
                elseif(!is_numeric($_POST["referencia"])||$_POST["referencia"]<=0)
                    echo "<span class='error'> * La referencia no es un número correcto * </span>";
                else
                    echo "<span class='error'> * Referencia repetida * </span>";
                echo "</td>";
            }
            ?>
        </tr>
        <tr>
            <td><label for="titulo">Título: </label></td>
            <td><input type="text" id="titulo" name="titulo" value="<?php if(isset($_POST["titulo"])) echo $_POST["titulo"]; ?>"/></td>
            <?php
            if(isset($_POST["btnAgregar"])&& $error_titulo)
            {
               
                    echo "<td><span class='error'> * Campo Vacío * </span></td>";
                
            }
            ?>
        </tr>
        <tr>
            <td><label for="autor">Autor: </label></td>
            <td><input type="text" id="autor" name="autor" value="<?php if(isset($_POST["autor"])) echo $_POST["autor"]; ?>"/></td>
            <?php
            if(isset($_POST["btnAgregar"])&& $error_autor)
            {
               
                    echo "<td><span class='error'> * Campo Vacío * </span></td>";
                
            }
            ?>
        </tr>
         <tr>
            <td><label for="descripcion">Descripción: </label></td>
            <td><textarea  id="descripcion" name="descripcion"><?php if(isset($_POST["descripcion"])) echo $_POST["descripcion"]; ?></textarea></td>
            <?php
            if(isset($_POST["btnAgregar"])&& $error_descripcion)
            {
               
                    echo "<td><span class='error'> * Campo Vacío * </span></td>";
                
            }
            ?>
        </tr>
        <tr>
            <td><label for="precio">Precio: </label></td>
            <td><input type="text" id="precio" name="precio" value="<?php if(isset($_POST["precio"])) echo $_POST["precio"]; ?>"/></td>
            <?php
            if(isset($_POST["btnAgregar"])&& $error_precio)
            {
                echo "<td>";
                if($_POST["precio"]=="")
                    echo "<span class='error'> * Campo Vacío * </span>";
                else
                    echo "<span class='error'> * El precio introducido no es una cantidad correcta * </span>";
                echo "</td>";
            }
            ?>
        </tr>
        <tr>
            <td><label for="foto">Portada (Máx. 750KB): </label></td><td><input type="file" name="foto" id="foto" accept="image/*"/></td>
            <?php
                if(isset($_POST["btnAgregar"])&& $error_foto)
                {
                    echo "<td>";
                    if($_FILES["foto"]["error"])
                        echo "<span class='error'>* Error en la subida del archivo al servidor *</span>";
                    elseif(!getimagesize($_FILES["foto"]["tmp_name"]))
                        echo "<span class='error'>* Error: no has seleccionado un archivo imagen *</span>";
                    else
                        echo "<span class='error'>* Error: el tamaño del archivo seleccionado supera los 750 KB *</span>";
                }
                    echo "</td>";
            ?>
        </tr>
         
        </table>
        <p>
            <input type="submit" name="btnAgregar" value="Agregar"/>
        </p>
    </form>
</body>
</html>