<?php
ini_set("max_execution_time",3600);
include('SimpleImage.php'); 

$dir = "/jorge/htdocs/";
$base = $dir;
$dest = $dir . "otros/GeneraGaleria/";
$maxSize = 1000;
$midSize = 500;
$tmbSize = 80;
$dirf = '';
$error = '';
$b=0;
$dirfin=1;
$marcadeagua = "firma_sm.png";

function marcadeagua($img_original, $img_marcadeagua, $img_nueva, $calidad)
{
	// obtener datos de la fotografia 
	$info_original = getimagesize($img_original); 
	$anchura_original = $info_original[0]; 
	$altura_original = $info_original[1]; 
	// obtener datos de la "marca de agua" 
	$info_marcadeagua = getimagesize($img_marcadeagua); 
	$anchura_marcadeagua = $info_marcadeagua[0]; 
	$altura_marcadeagua = $info_marcadeagua[1]; 
	// calcular la posición donde debe copiarse la "marca de agua" en la fotografia 
	/* 
	// Posicion: Centrado
	$horizmargen = ($anchura_original - $anchura_marcadeagua)/2; 
	$vertmargen = ($altura_original - $altura_marcadeagua)/2; 
	*/
	// Posicion: abajo a la izquierda
	$horizmargen = 10; 
	$vertmargen = ($altura_original - $altura_marcadeagua)-10; 
	// crear imagen desde el original 
	$original = ImageCreateFromJPEG($img_original); 
	ImageAlphaBlending($original, true); 
	// crear nueva imagen desde la marca de agua 
	$marcadeagua = ImageCreateFromPNG($img_marcadeagua); 
	// copiar la "marca de agua" en la fotografia 
	ImageCopy($original, $marcadeagua, $horizmargen, $vertmargen, 0, 0, $anchura_marcadeagua, $altura_marcadeagua); 
	// guardar la nueva imagen 
	ImageJPEG($original, $img_nueva, $calidad); 
	// cerrar las imágenes 
	ImageDestroy($original); 
	ImageDestroy($marcadeagua); 
} 

?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta description="Mi sitio" />
        <title>Crear Galeria para DropBox</title>
        <style type="text/css">
        html body * {
        	display: inline-block;
        	height: auto;
        	margin: 0;
        	padding: 0;
        	width: 100%;
        }
        section {
        	height: auto;
        	width: 100%;
        }
        article {
        	height: auto;
        	width: 100%;
        }
        ul {
        	height: auto;
        	width: 100%;
        }
        ul li {
        	border: solid 1px transparent; 
        	height: auto;
        	list-style: none;
        	margin: 1px 0px;
        	padding: 2px;
        	width: 100%;
        }
        ul li:first-child {
        	text-align: center;
        }
        ul li.errores {
        	border: solid 1px red; 
        }
        ul li b {
        	display: inline;
        	height: auto;
        	width: auto;
        }
        ul li label {
        	color: red;
        	display: inline;
        	height: auto;
        	width: auto;
        }
        ul li input {
        	display: inline;
        	height: auto;
        	width: 300px;
        }
        form ul li,form ul li:first-child {
        	text-align: left;
        }
        </style> 
    </head>
	<body>
		<section>
			<article>
				<form action="./index.php" method="POST" id="logsusu" name="logsusu" enctype="multipart/form-data"> 
				<ul>
					<li>Datos para crear galeria:</li>
					<li>
						<input type="text" id="carpeta" name="carpeta" placeholder="Carpeta ..." />
					</li>
					<li>
						<input type="text" id="titulo" name="titulo" placeholder="Titulo ..." />
					</li>
					<li>
						<input type="text" id="galeria" name="galeria" placeholder="Galeria ..." />
					</li>
					<li>
						<input type="submit" value="Crear" />
					</li>
				</ul>
				</form>
			</article>
			<article>
				
				<?php
				if($_POST && strlen($_POST['carpeta'])>0) {
					echo "<ul><li>Resultados:</li>";
					$dir = $dir . $_POST['carpeta'] . "/";
					$dirf = $_POST['galeria'] . "/";
					if(!file_exists($dest)) {
						echo "<li class='errores'>Carpeta DropBox de destino NO Encontrada.</li>";
					} else {
						//echo "DIR: " . $dir . "<br>";
						//echo "DIR: " . $dest.$dirf . "<br>";
						if(!mkdir($dest.$dirf,0777,true)) {
							echo "<li class='errores'>No se puede crear galeria [".$dest.$dirf."].</li>";
						} 
						$gal = $_POST['galeria'];
						$titulogal = addslashes($_POST['titulo']);
						$res=0;
						echo "<li>";
						foreach (scandir($dir) as $item) {
							if ($item <> '.' and $item <> '..' and $item <> 'Thumbs.db'){
								$imagen = $dir . $item;
								$imagenh = "hd_res_" . $item;
								$imagenr = "res_" . $item;
								$imagenb = "th_res_" . $item;
								//echo "Procesando archivo: " . $imagen . " => " . $base . "otros/generagaleria/".$dirf . $imagenr . "<br>";
								$size = GetImageSize($imagen);
								$anchura=$size[0];
								$altura=$size[1]; 
								if($anchura>$altura){
									if($res<10){
								 		$image = new SimpleImage(); 
								 		$image->load($imagen); 
								 		$image->resizeToWidth($maxSize);
								 		$image->save($base . "otros/generagaleria/".$dirf . "" . $imagenh);
								 		echo "insert into fotos (id_galeria,description,alto_ancho) "
											. "values(" . $gal . ",'" . $imagenh . "',2);<br>";
									}
							 		$image = new SimpleImage(); 
							 		$image->load($imagen); 
							 		$image->resizeToWidth($midSize);
							 		$image->save($base . "otros/generagaleria/".$dirf . "" . $imagenr);
							 		echo "insert into fotos (id_galeria,description,alto_ancho) "
											. "values(" . $gal . ",'" . $imagenr . "',1);<br>";
							 		$res++;
								}else{
							 		$image = new SimpleImage(); 
							 		$image->load($imagen); 
							 		$image->resizeToHeight($midSize); 
							 		$image->save($base . "otros/generagaleria/".$dirf . "" . $imagenr);
							 		echo "insert into fotos (id_galeria,description,alto_ancho) "
											. "values(" . $gal . ",'" . $imagenr . "',0);<br>";
								}
								if(file_exists($base . "otros/generagaleria/".$dirf . "" . $imagenh)){
									$origenh = $base . "otros/generagaleria/".$dirf . "" . $imagenh;
									$destinoh = $base . "otros/generagaleria/".$dirf . "" . "n_" . $imagenh;
									$destino_temporal = tempnam("tmp/","tmp");
									marcadeagua($origenh, $marcadeagua, $destino_temporal, 100);
									$fph=fopen($destinoh,"w");
									fputs($fph,fread(fopen($destino_temporal,"r"),filesize($destino_temporal)));
									fclose($fph);
									unlink($origenh);
									rename($destinoh,$origenh);
								}
								if(file_exists($base . "otros/generagaleria/".$dirf . "" . $imagenr)){
									$origen = $base . "otros/generagaleria/".$dirf . "" . $imagenr;
									$destino = $base . "otros/generagaleria/".$dirf . "" . "n_" . $imagenr;
									$destino_temporal = tempnam("tmp/","tmp");
									marcadeagua($origen, $marcadeagua, $destino_temporal, 100);
									$fp=fopen($destino,"w");
									fputs($fp,fread(fopen($destino_temporal,"r"),filesize($destino_temporal)));
									fclose($fp);
									unlink($origen);
									rename($destino,$origen);

									copy($base . "otros/generagaleria/".$dirf . "" . $imagenr, $base . "otros/generagaleria/".$dirf . "" . $imagenb);
									if(strlen($imagenb)>1){
										if(file_exists($base . "otros/generagaleria/".$dirf . "" . $imagenb)){
											$origenb = $base . "otros/generagaleria/".$dirf . "" . $imagenb;
									 		$image = new SimpleImage(); 
									 		$image->load($origenb); 
											if($anchura>$altura){
									 			$image->resizeToWidth($tmbSize);
									 		} else {
									 			$image->resizeToHeight($tmbSize);
									 		}
									 		$image->save($origenb);
										}
									}
								}
							 	$b++;
						 	}
						}
						if(strlen($_POST['titulo'])>0){
							echo "insert into galerias values(" . $gal . ",'".$titulogal."'," . $b . ");<br>";
						}
						echo "</li>";
					}
					echo "</ul>";
				}
				?>
			</article>
		</section>
	</body>
</html>