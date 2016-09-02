<?php
session_start();

//include ("../jpgraph/jpgraph.php");
//include ("../jpgraph/jpgraph_line.php");
//include ("jpgraph-4.0.1/src/jpgraph.php");
include ("jpgraph-4.0.1/src/jpgraph_line.php");
require_once ('mysqli_connect.php');

// Some data
/*$q="SELECT nombre_estado, toneladas, superficie_cosechada, rendimiento,anio
	FROM productos, produccion, estados
	WHERE clave_producto=productos_clave_producto AND clave_estado = estados_clave_estado AND
	clave_estado=$es AND clave_producto=$pr
";*/

//$_SESSION['consulta']=$q;

//$r = @mysqli_query ($dbc, $q);
$r = @mysqli_query ($dbc, $_SESSION['consulta']);
$row = @mysqli_fetch_array ($r, MYSQLI_NUM);

//while($row = mysql_fetch_array($r))

while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
$ydata[] = $row['toneladas'];
}

//$ydata = array(11.5,3,8,12,5,1,9,13,5,7);

// Create the graph. These two calls are always required
$graph = new Graph(800,600,"auto");
$graph->SetScale("textlin");
$graph->img->SetAntiAliasing();
$graph->xgrid->Show();

// Create the linear plot
$lineplot=new LinePlot($ydata);
$lineplot->SetColor("black");
$lineplot->SetWeight(3);
$lineplot->SetLegend("Toneladas");

// Setup margin and titles
//$graph->img->SetMargin(40,20,20,40);  //valores originales
$graph->img->SetMargin(100,50,100,50);
$graph->title->Set("Comportamiento de produccion");
$graph->xaxis->title->Set("A&ntilde;o");
//$graph->yaxis->title->Set("Producci�n");
//$graph->ygrid->SetFill(true,'#CCCCCC@0.5','#0000FF@0.3');
$graph->ygrid->SetFill(true,'#bddef7@0.2','#fafafa@0.1');
$graph->title->SetFont(FF_ARIAL,FS_BOLD,12);
//$graph->subtitle->Set('(Updated every 5 minutes)');
$graph->subtitle->SetFont(FF_ARIAL,FS_ITALIC,10);
//$graph->SetShadow();
// Add the plot to the graph
$graph->Add($lineplot);
// Display the graph


/////7
/*$ydata2  = array( 1000000 , 1900000 , 1500000 , 700000 , 2200000 , 1400000 , 500000 , 900000 );
// Create a new data series with a different color
$lineplot2 = new  LinePlot ( $ydata2 );
$lineplot2->SetWeight ( 2 );
// Also add the new data series to the graph
$graph->Add( $lineplot2 );
*/
/////

$graph->Stroke();
//echo $q;

?>
