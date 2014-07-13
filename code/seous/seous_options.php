<?php
/**
 *
 * Info: Definición de variables para configurar SeoUs
 **/


function seous_var_config(){
	global $seous_config, $wp_query, $post;

	$seous_config['define_pt_for_title_and_desc'] = '';  // Use:  array('page', 'post');
	$seous_config['gatc'] = 'UA-XXXXXXXx-X';  // Use:  UA-XXXXXXXx-X;
	$seous_config['domain'] = 'example.com';  // Use:  example.com; home_url()

	// <title>
	$seous_config['general_title_site'] = 'Antes muerto que sin title';
	$seous_config['title_404'] = 'Ups!! alguien no ha hecho sus deberes';
	if ( is_search() ){
		$count = $wp_query->post_count;
		$term = $wp_query->query;
		$seous_config['title_search'] = __('Para el termino ','seous') . $term['s'] . __(' hemos encontrado ','seous')  . $count . __(' resultados','seous');
	}
	$seous_config['title_archive'] = 'Listado de items...';

	// <description>
	$seous_config['seousdescription'] = 'Este es un gran sitio con una descripcion correcta';

	// owner
	$seous_config['autor_owner_copy'] = 'Nosotros seous';
	$seous_config['lang'] = get_locale();
	$seous_config['locality'] = 'city, country';

	/* distribution
    	Global - indicates that your webpage is intended for everyone,
    	Local - intended for local distribution of your document,
    	IU - Internal Use, not intended for public distribution.
	*/
    $seous_config['distribution'] = 'Global';


	$seous_config['alexaVerifyID']		= 'alexaVerifyID'; 	// Alexa http://www.alexa.com/siteowners/claim
	$seous_config['msvalidate']			= 'msvalidate'; 	// Bing http://www.bing.com/toolbox/webmaster/
	$seous_config['googlesite']			= 'googlesite'; 	// Google www.google.es/webmasters/tools/
	$seous_config['pdomainverify']		= 'pdomainverify'; 	// Pinterest https://www.pinterest.com/
	$seous_config['yandexverification'] = 'yandexverification'; 	// Yandex https://webmaster.yandex.com/

	//revisit-after
	$seous_config['revisit'] = '7';	//number of days @use: 14



/*_________________________________*\

			bola extra Nº1
\*_________________________________*/

	$seous_config['schemaname'] = seous_name_social();
	$seous_config['schemadescription'] = seous_description_social();
	$seous_config['schemaimage'] = seous_image_social( 'http://example.com/imageprofile.png' );
	$seous_config['googleplus'] = get_user_meta( $post->post_author, 'googleplus', true);
	$seous_config['googleplus_page'] = get_user_meta( $post->post_author, 'googleplus_page', true);


	// Twitter - https://dev.twitter.com/docs/cards
	$seous_config['twitter_site'] = '@twitter_site';
	$seous_config['twitter_title'] = get_the_title( $post->ID );
	$seous_config['twitter_description'] = seous_description_social();
	$seous_config['twitter_creator'] = get_the_author_meta( 'twitter', $post->post_author );;
	$seous_config['twitter_image_src'] = seous_image_social( 'http://example.com/imagepost.png' );

	// Facebook - http://ogp.me/
	$seous_config['og_title'] = get_the_title( $post->ID );
	$seous_config['og_description'] = seous_description_social();
	$seous_config['og_image'] = seous_image_social( 'http://example.com/imagepost.png' );
	$seous_config['og_site_name'] = get_bloginfo('name');
	$seous_config['og_admins'] = 'facebook_user_id';

/*_________________________________*\

			bola extra Nº2
\*_________________________________*/
	$seous_config['pt_news'] = 'news'; // postype de noticias para medios
	$seous_config['news_keywords'] = 'Listar las frases, o palabras para, ser encontrado';


/*_________________________________*\

			bola extra Nº3
\*_________________________________*/

	$seous_config['mail404'] = '404@seous.es';
	$seous_config['mail_name_site'] = get_bloginfo('name');



	$GLOBALS['seous_config'] = $seous_config;

}
add_action('wp', 'seous_var_config');