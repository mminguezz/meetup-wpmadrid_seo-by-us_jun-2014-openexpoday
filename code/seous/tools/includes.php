<?php
/**
 * 
 * Info: Include, funciones de ayuda.
 *
 **/

function seous_gatc( ){
	global $seous_config;
	$gatc = "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){";
	$gatc .= "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),";
	$gatc .= "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)";
	$gatc .= "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');";
	$gatc .= "ga('create', '". $seous_config['gatc'] . "', '" . $seous_config['domain'] . "');";
	$gatc .= "ga('send', 'pageview');";
	$gatc .= "</script>";
	return $gatc;
}


function seous_image_social( $imagedefault, $img_id = NULL ){
	global $post;

	if( !$img_id ){
		$img_id = get_the_ID();
	}
	$class_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $img_id ) );

	if ( has_post_thumbnail( $img_id ) ) {
		$miniatura = $class_image_url[0];
	} else {
		$miniatura = $imagedefault;
	}
	return $miniatura;
}

function seous_name_social(){
	global $seous_config, $post;
	$title = $seous_config['general_title_site'];

	if ( is_single() || is_page() ){
		$title = get_post_meta( get_the_id(), '_seoustitle', true );
	}

	if ( ! $title ){
		$title = get_bloginfo('name');
	}

	return $title;
}

function seous_description_social(){
	global $seous_config, $post;
	$description = $seous_config['seousdescription'];

	if ( is_single() || is_page() ){
		$description = get_post_meta( get_the_id(), '_seousdescription', true );
	}

	if ( ! $description  ){
		$description = get_bloginfo('description');
	}

	return $description;
}



/*_________________________________*\

			bola extra Nº3
\*_________________________________*/

function seous_404_mail() {
	global $seous_config, $post;
	if ( ! is_404() ) {
		return;
	}

	$mail = 'Seo Us 404 <' .$seous_config['mail404'] . '>' . "\r\n";;
	if ($mail == ''){
		return;
	}


	// IP del cliente
	$remote_ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "(Sin IP)";
	$message = __('Se recibio una visita de la IP: ', 'seous') . $remote_ip;

	// ISP del cliente
	$remote_isp = gethostbyaddr($remote_ip);
	$message .= __(' el ISP del cliente es ', 'seous') . $remote_isp;

	// Aqui la pagina que lo refirio
	$referer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? strtolower( $_SERVER['HTTP_REFERER'] ) : "desconocida";
	$message .= __(' la pagina que lo envio aqui es ', 'seous') .$referer;


	wp_mail( $mail, '404 - '. $seous_config['mail_name_site'], $message );

}




?>