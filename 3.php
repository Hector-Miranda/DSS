<?php # asistencia_carnet.php 
		# manuelyz@hotmail.com
		
require_once ('mysqli_connect.php');
$page_title = ':: Sistema de soporte a las decisiones - Cuadro comparativo ::';
include ('comunes/encabezado.html');

if (isset($_POST['submitted'])) { // Handle the form.
	
	// Validate the incoming data...
	$errors = array();

	if ( isset($_POST['producto']) && ($_POST['producto'] == 'producto_existente') && ($_POST['producto_existente'] > 0) ) { // Existing artist.
		$pr = (int) $_POST['producto_existente'];
	} else { // No artist selected.
		$errors[] = 'No se ha seleccionado el producto.';
	}
	
	if ( isset($_POST['anio']) && ($_POST['anio'] == 'anio_existente') && ($_POST['anio_existente'] > 0) ) { // Existing artist.
		$a = (int) $_POST['anio_existente'];
	} else { // No artist selected.
		$errors[] = 'No se ha seleccionado el a&ntilde;o.';
	}
	
	$consulta = "select (now()) as numero";
	$resultado = @mysqli_query ($dbc, $consulta);
	$resultarr = mysqli_fetch_assoc($resultado);
	$total = $resultarr["numero"];
			
		
	if (empty($errors)) { // If everything's OK.

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//*************************************************************************************************************************************
		echo '<h1>Comparaci&oacute;n de ';
		//Obtiene el nombre dinámicamente de acuerdo al índice enviado en la lista desplegable
		$q = "SELECT nombre_producto FROM productos where clave_producto= $pr";
		$r = @mysqli_query ($dbc, $q);
		$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
		echo $row['nombre_producto'];
		echo ' en el a&ntilde;o ',$a,'</h1>';
		
		require_once ('mysqli_connect.php');
		
		// Number of records to show per page:
		$display = 32; // corresponde a los 32 estados de la República Mexicana, y no queremos que muestra páginas.
		
		// Determine how many pages there are...
		if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
			$pages = $_GET['p'];
		} else { // Need to determine.
			// Count the number of records:
			$q = "SELECT COUNT(clave_estado) FROM estados";
			$r = @mysqli_query ($dbc, $q);
			$row = @mysqli_fetch_array ($r, MYSQLI_NUM);
			$records = $row[0];
			// Calculate the number of pages...
			if ($records > $display) { // More than 1 page.
				$pages = ceil ($records/$display);
			} else {
				$pages = 1;
			}
		} // End of p IF.
		
		// Determine where in the database to start returning results...
		if (isset($_GET['s']) && is_numeric($_GET['s'])) {
			$start = $_GET['s'];
		} else {
			$start = 0;
		}
		
		
		// Determine the sort...
		$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'z';
		
		// Determine the sorting order:
		// Problemas para determinar el sort, se removió =/
		switch ($sort) {
			case 'z':
				$order_by = 'toneladas ASC';
				break;
			case 'a':
				$order_by = 'nombre_estado ASC';
				break;
			case 'b':
				$order_by = 'superficie_cosechada ASC';
				break;
			case 'c':
				$order_by = 'rendimiento ASC';
				break;
			default:
				$order_by = 'toneladas ASC';
				$sort = 'z';
				break;
		}
		
		// Make the query:
		$q = "SELECT nombre_estado, toneladas, superficie_cosechada, rendimiento
			FROM productos, produccion, estados
			WHERE clave_producto=productos_clave_producto AND clave_estado = estados_clave_estado AND
			anio = $a AND clave_producto = $pr
			order by toneladas DESc";
		$r = @mysqli_query ($dbc, $q); // Run the query.
		
		// Table header:
		echo '<table align="center" cellspacing="5" cellpadding="5" width="100%">
		<tr>
			<td align="center"><b>Estado</b></td>
			<td align="center"><b>Producci&oacute;n<br>(toneladas)</b></td>
			<td align="center"><b>Superficie cosechada<br>(hect&aacute;reas)</b></td>
			<td align="center"><b>Rendimiento<br>(toneladas/hect&aacute;reas)</b></td>
		</tr>
		';
		
		// Fetch and print all the records....
		$bg = '#eeeeee'; 
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
			echo '<tr bgcolor="' . $bg . '">
				<td align="left">' . $row['nombre_estado'] . '</td>
				<td align="right">' . number_format($row['toneladas'],2, '.', ' ') . '</td>
				<td align="right">' . number_format($row['superficie_cosechada'],2, '.', ' ') . '</td>
				<td align="right">' . number_format($row['rendimiento'],2, '.', ' ') . '</td>
			</tr>
			';
		} // End of WHILE loop.
		
		echo '</table>';
		mysqli_free_result ($r);
		//mysqli_close($dbc); <--!!!!! Mantener cerrado si se van a dejar los filtros abajo
		
		// Make the links to other pages, if necessary.
		if ($pages > 1) {	
			echo '<br /><p class="style10">';
			$current_page = ($start/$display) + 1;	
			// If it's not the first page, make a Previous button:
			if ($current_page != 1) {
				echo '<a href="3.php?s=' . ($start - $display) . '&p=' . $pages . '&sort=' . $sort . '">Anterior</a> ';
			}	
			// Make all the numbered pages:
			for ($i = 1; $i <= $pages; $i++) {
				if ($i != $current_page) {
					echo '<a href="3.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . '&sort=' . $sort . '">' . $i . '</a> ';
				} else {
					echo $i . ' ';
				}
			} // End of FOR loop.	
			// If it's not the last page, make a Next button:
			if ($current_page != $pages) {
				echo '<a href="3.php?s=' . ($start + $display) . '&p=' . $pages . '&sort=' . $sort . '">Siguiente</a>';
			}	
			echo '</p>'; // Close the paragraph.	
		} // End of links section.	//*************************************************************************************************************************************		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		// Check the results...
		if (empty($errors)) {
			// Print a message:
			echo '<br><h2>Producci&oacute;n agr&iacute;cola</h2>
				<p>Ciclo: Oto&ntilde;o-Invierno ', $a, '</br>Modalidad: Riego + Temporal</p>
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
	//echo 'Please reselect the print image and try again.</p>';
}

// Display the form...
?>
<h1>Generar cuadro comparativo de los estados</h1>
<form enctype="multipart/form-data" action="3.php" method="post">
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
        <td><p>A&ntilde;o:</p></td>
        <td><p><input type="hidden" name="anio" value="anio_existente" <?php if (isset($_POST['anio']) && ($_POST['anio'] == 'anio_existente') ) echo ' checked="checked"'; ?>/>
            <select name="anio_existente"><option>Seleccione el a&ntilde;o...</option>
            <?php // Retrieve all the artists and add to the pull-down menu.
                $q = "SELECT anio FROM produccion 
					GROUP BY anio
					ORDER BY anio ASC";
                $r = mysqli_query ($dbc, $q);
                if (mysqli_num_rows($r) > 0) {
                    while ($row = mysqli_fetch_array ($r, MYSQLI_NUM)) {
                        echo "<option value=\"$row[0]\"";
						// Check for stickyness:
						if (isset($_POST['anio_existente']) && ($_POST['anio_existente'] == $row[0]) ) 
							echo ' selected="selected"';
							echo ">$row[0]</option>\n";
					}
				} else {
					echo '<option>No hay a&ntilde;os registrados.</option>';
				}
				mysqli_close($dbc); // Close the database connection. //Por seguridad =)
			?>
			</select></p></td>
		<tr>        			
		<!--</fieldset>-->
		<td><p><input type="submit" name="submit" value="Realizar comparaci&oacute;n" />
			<input type="hidden" name="submitted" value="TRUE" /></p></td>
		</tr>
	</table>
</form>

<?php
include ('comunes/pie.html');
?>