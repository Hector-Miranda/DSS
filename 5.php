<?php # 5.php
		# manuelyz@hotmail.com
session_start();
require_once ('mysqli_connect.php');
$page_title = ':: Sistema de soporte a las decisiones - Gr&aacute;fico ::';
include ('comunes/encabezado.html');

if (isset($_POST['submitted'])) { // Handle the form.

	// Validate the incoming data...
	$errors = array();

	if ( isset($_POST['producto']) && ($_POST['producto'] == 'producto_existente') && ($_POST['producto_existente'] > 0) ) { // Existing artist.
		$pr = (int) $_POST['producto_existente'];
	} else { // No artist selected.
		$errors[] = 'No se ha seleccionado el producto.';
	}

	if ( isset($_POST['estado']) && ($_POST['estado'] == 'estado_existente') && ($_POST['estado_existente'] > 0) ) { // Existing artist.
		$es = (int) $_POST['estado_existente'];
	} else { // No artist selected.
		$errors[] = 'No se ha seleccionado el estado.';
	}


	if (empty($errors)) { // If everything's OK.

		////////////////////////////////////////////////////////////////////////////
		//**************************************************************************
		echo '<h1>Gr&aacute;fico de ';
		//Obtiene el producto dinámicamente de acuerdo al índice enviado en la lista desplegable
		$q = "SELECT nombre_producto FROM productos where clave_producto= $pr";
		$r = @mysqli_query ($dbc, $q);
		$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
		echo $row['nombre_producto'];
		//Obtiene el estado dinámicamente de acuerdo al índice enviado en la lista desplegable
		$j = "SELECT nombre_estado FROM estados where clave_estado= $es";
		$k = @mysqli_query ($dbc, $j);
		$fila = mysqli_fetch_array($k, MYSQLI_ASSOC);
		echo ' en ', $fila['nombre_estado'], '</h1>';

		require_once ('mysqli_connect.php');


		// Make the query:
		$q = "SELECT nombre_estado, toneladas, superficie_cosechada, rendimiento,anio
			FROM productos, produccion, estados
			WHERE clave_producto=productos_clave_producto AND clave_estado = estados_clave_estado AND
			clave_estado=$es AND clave_producto=$pr";

		 $_SESSION['consulta']=$q;

		$r = @mysqli_query ($dbc, $q); // Run the query.


		//************************************************************************//

		// Check the results...
		if (empty($errors)) {
			?>
			<p align="center"><img src="grafico_linea.php" align="center" border="0">
                            </ img></p>
                            <?PHP
			// Print a message:
			echo '<br><h2>Producci&oacute;n agr&iacute;cola</h2>
				<p>Ciclo: Oto&ntilde;o-Invierno ', /*$a,*/ '</br>Modalidad: Riego + Temporal</p>
				<p>Se utiliza el m&eacute;todo de suavizaci&oacute;n exponencial simplre porque es un caso especial de promedios m&oacute;viles. Se cuenta con la libertad de elegir la "importancia" de la historia, es decir, los registros pasados. De esta forma, no se estancan las proyecciones realizadas. Tambi&eacute;n es importante recalcar que si di&oacute; especial &eacute;nfasis al ma&iacute;z debido al papel que representa en la alimentaci&oacute;n de los mexicanos; al generar la gr&aacute;fica notaremos la tendencia ascendente que dibuja, especial para este tipo de procedimientos. Como comentario, la elecci&oacute; de &alpha; se hizo buscando reducir lo m&aacute; el error cuadrado medio.</p>
				';


			// Clear $_POST:
			//	$_POST = array();			//se anula para conservar lo "sticky"
		} else { // Error!
			echo '<p class="error">Ha ocurrido un error en el sistema.<br />';
			echo '</p><p>Intentelo de nuevo.</p><p><br /></p>';
		}

		//mysqli_stmt_close($stmt);
	} // End of $errors IF.

} // End of the submission IF.

// Check for any errors and print them:
if ( !empty($errors) && is_array($errors) ) {
	echo '<h1>&iexcl;Error!</h1>
		<p class="error">Han ocurrido los siguientes errores:<br />';
	foreach ($errors as $msg) {
		echo " - $msg<br />\n";
	}
	echo '</p><p>Por favor, intente de nuevo.</p><p><br /></p>';
}

// Display the form...
?>
<h1>Generar gr&aacute;fico de los estados</h1>
<form enctype="multipart/form-data" action="5.php" method="post">
	<!--
	<input type="hidden" name="MAX_FILE_SIZE" value="524288" />
	-->
	<!--<fieldset><legend>...</legend>-->
	<table>
		<tr>
        <td><p>Producto:</p></td>
        <td><p><input type="hidden" name="producto" value="producto_existente" <?php if (isset($_POST['producto']) && ($_POST['producto'] == 'producto_existente') ) echo ' checked="checked"'; ?>/>
            <select name="producto_existente"><option>Seleccione el producto...</option>
            <?php // Retrieve all the artists and add to the pull-down menu.
                $q = "SELECT * FROM productos ORDER BY nombre_producto ASC";
                $r = mysqli_query ($dbc, $q);
                if (mysqli_num_rows($r) > 0) {
                    while ($row = mysqli_fetch_array ($r, MYSQLI_NUM)) {
                        echo "<option value=\"$row[0]\"";
						// Check for stickyness:
						if (isset($_POST['producto_existente']) && ($_POST['producto_existente'] == $row[0]) )
							echo ' selected="selected"';
							echo ">$row[1]</option>\n";
					}
				} else {
					echo '<option>No hay productos registrados.</option>';
				}
				//mysqli_close($dbc); // Close the database connection. // ¡¡sólo va en el último!!
			?>
			</select></p></td>
		<tr>

		<tr>
        <td><p>Estado:</p></td>
        <td><p><input type="hidden" name="estado" value="estado_existente" <?php if (isset($_POST['estado']) && ($_POST['estado'] == 'estado_existente') ) echo ' checked="checked"'; ?>/>
            <select name="estado_existente"><option>Seleccione el estado...</option>
            <?php // Retrieve all the artists and add to the pull-down menu.
                $q = "SELECT * FROM estados
					GROUP BY nombre_estado
					ORDER BY nombre_estado ASC";
                $r = mysqli_query ($dbc, $q);
                if (mysqli_num_rows($r) > 0) {
                    while ($row = mysqli_fetch_array ($r, MYSQLI_NUM)) {
                        echo "<option value=\"$row[0]\"";
						// Check for stickyness:
						if (isset($_POST['estado_existente']) && ($_POST['estado_existente'] == $row[0]) )
							echo ' selected="selected"';
							echo ">$row[1]</option>\n";
					}
				} else {
					echo '<option>No hay estados registrados.</option>';
				}
				mysqli_close($dbc); // Close the database connection. //Por seguridad =)
			?>
			</select></p></td>
		<tr>
		<!--</fieldset>-->
		<td><p><input type="submit" name="submit" value="Generar gr&aacute;fico" />
			<input type="hidden" name="submitted" value="TRUE" /></p></td>
		</tr>
	</table>
</form>

<?php
include ('comunes/pie.html');
?>
