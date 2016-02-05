<?php
ini_set("max_execution_time",3600);
include('SimpleImage.php'); 

//$dir = "../";
//$dest = "/jorge/dropbox/public/";
$dir = "/jorge/htdocs/";
$dest = "./";
$maxSize = 500;
$tmbSize = 80;
$dirf = '';
$error = '';
$marcadeagua = "firma_sm.png";
$b=0;
$dirfin=1;

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
					if(!file_exists($dest)) {
						echo "<li class='errores'>Carpeta DropBox de destino NO Encontrada.</li>";
					} else {
						foreach (scandir($dest) as $item) {
							if ($item <> '.' and $item <> '..' and $item <> 'Thumbs.db'){
								$dirf = $item;
							}
						}
						if(!is_integer($dirf)){$dirf=$_POST['galeria'];} else {
						$dirf++;}
						if(!mkdir($dest.$dirf,0777,true)) {
							echo "<li class='errores'>No se puede crear galeria.</li>";
						} else {
							$gal = $dirf;
							$titulogal = addslashes($_POST['titulo']);
							foreach (scandir($dir) as $item) {
								if ($item <> '.' and $item <> '..' and $item <> 'Thumbs.db'){
									$imagen = $dir . $item;
									$imagenr = "res_" . $item;
									$imagent = "th_res_" . $item;
									//echo "Procesando archivo: " . $imagen . "<br>";
									$size = GetImageSize($imagen);
									$anchura=$size[0];
									$altura=$size[1]; 
									$res=0;
									if($anchura>$altura){
								 		$image = new SimpleImage(); 
								 		$image->load($imagen); 
								 		$image->resizeToWidth($maxSize); 
								 		$image->save($dir . "/" . $imagenr);
								 		$image->resizeToWidth($tmbSize); 
								 		$image->save($dest . $dirf . "/" . $imagent);
									}else{
								 		$image = new SimpleImage(); 
								 		$image->load($imagen); 
								 		$image->resizeToHeight($maxSize); 
								 		$image->save($dir . "/" . $imagenr);
								 		$image->resizeToHeight($tmbSize); 
								 		$image->save($dest . $dirf . "/" . $imagent);
									}
									$origen = $dir . $imagenr;
									$destino = $dir . "n_" . $imagenr;
									copy($origen,$destino);
									$dest_dbox = $dest . $dirf . "/" . $imagenr;
									if(file_exists($dest . $dirf . "/" . $imagent)){
										$error = 'Imagen Thumbnail NO Encontrada';
									}
									if(file_exists($origen)){
										$destino_temporal = tempnam("tmp/","tmp");
										marcadeagua($origen, $marcadeagua, $destino_temporal, 100);

										// guardamos la imagen
										$fp=fopen($destino,"w");
										fputs($fp,fread(fopen($destino_temporal,"r"),filesize($destino_temporal)));
										fclose($fp);
										unlink($origen);
										rename($destino,$origen);
								 		$image = new SimpleImage(); 
								 		$image->load($origen); 
										if($anchura>$altura){
											$res=1;
									 		$image->resizeToWidth($maxSize); 
										} else {
											$res=0;
									 		$image->resizeToHeight($maxSize); 
									 	}
								 		$image->save($dest . $dirf . "/" . $imagenr);
								 		if(file_exists($dest . $dirf . "/" . $imagenr)){
											unlink($origen);
								 		} else {
								 			$error = 'Imagen de marca de agua NO Encontrada';
								 		}
									} else {
							 			$error = 'Imagen Resized NO Encontrada';
							 		}
							 		if(file_exists($dest . $dirf . "/" . $imagent) && file_exists($dest . $dirf . "/" . $imagenr)){
										//echo "Proceso exitoso.<br>";
										echo "insert into fotos (id_galeria,description,alto_ancho) "
											. "values(" . $gal . ",'" . $imagenr . "'," . $res . ");<br>";
										$b++;
							 		} else {
										echo "<li class='errores'>Proceso fallido: ".$error.".</li>";
							 		}
							 	}
							}
							if(strlen($_POST['titulo'])>0){
								echo "insert into galerias values(" . $gal . ",'".$titulogal."'," . $b . ");<br>";
							}
						}
					}
					echo "</ul>";
				}
				?>
			</article>
		</section>
	</body>
</html>