<?php
$consulta="select * from libros";
$resultado=mysqli_query($conexion,$consulta);
if($resultado)
{
    echo "<h3>Listado de los libros</h3>";
    echo "<table id='t_libros'>";
    echo "<tr><th>Ref</th><th>Título</th><th>Acción</th></tr>";
    while($libros=mysqli_fetch_assoc($resultado))
    {
        echo "<tr>";
        echo "<td>".$libros["referencia"]."</td>";
        echo "<td>".$libros["titulo"]."</td>";
        echo "<td><form action='gest_libros.php' method='post'><button class='sin_boton' type='submit' name='btnBorrar' value='".$libros["referencia"]."'>Borrar</button> - <button class='sin_boton' type='submit' name='btnEditar' value='".$libros["referencia"]."'>Editar</button></form></td>";
        echo "</tr>";
    }
    echo "</table>";
    mysqli_free_result($resultado);
}
else
{
    mysqli_close($conexion);
    session_destroy();
    die("<p>Error en la consulta Nº: ".mysqli_errno($conexion). " : ".mysqli_error($conexion)."</p></body></html>");
}
?>