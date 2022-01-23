<?php
$consulta="select * from libros";
$resultado=mysqli_query($conexion,$consulta);
if($resultado)
{
    echo "<h3>Listado de los libros</h3>";
    echo "<div class='contenedor'>";
    while($libros=mysqli_fetch_assoc($resultado))
    {
        echo "<div>";
        echo "<img src='Images/".$libros["portada"]."' alt='".$libros["titulo"]."' title='".$libros["titulo"]."'/>";
        echo "<p>".$libros["titulo"]." - ".$libros["precio"]." €</p>";
        echo "</div>";
    }

    echo "</div>";
    mysqli_free_result($resultado);
}
else
{
    mysqli_close($conexion);
    session_destroy();
    die("<p>Error en la consulta Nº: ".mysqli_errno($conexion). " : ".mysqli_error($conexion)."</p></body></html>");
}

?>