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
            .contenedor{width:100%}
            .contenedor div{width:30%;float:left; text-align: center;margin-top: 2.5%;margin-left:2.5%;}
            .contenedor div img{width:80%;}
        </style>
    </head>
    <body>
        <h1>Librería</h1>
        <div>Bienvenido <strong><?php echo $_SESSION["usuario"];?></strong> - 
            <form class="enlinea" method="post" action="index.php">
                <button class="sin_boton" type="submit" name="btnCerrarSesion">Salir</button>
            </form>
        </div>
        <?php
            require "vistas/vista_libros_tres_en_tres.php";
        ?>
    </body>
</html>