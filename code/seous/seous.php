<?php
/**
 * Plugin Name: SeoUs
 * Plugin URI: http://seous.intheloop.es/
 * Description: SeoUs es un simple plugin para mejorar el posicionamiento en buscadores "onpage" para WordPress
 * Author: @m_minguezz & @BRodrigalvarez
 * Author URI: #
 * Version: 0.0.2
 * Text Domain: seous
 * Domain Path: /languages/
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */


// Constante de url de tools
define('SEO_TOOLS', plugin_dir_path( __FILE__) . 'tools' );

require_once ( plugin_dir_path( __FILE__ ) . 'seous_options.php' );
require_once ( SEO_TOOLS . '/includes.php' );
require_once ( SEO_TOOLS . '/metabox.php' );
require_once ( SEO_TOOLS . '/metauser.php' );

/*_________________________________*\

			Admin page
\*_________________________________*/

//Añadir pagina al administrador
add_action( 'admin_menu', 'seous_menu_page' );
function seous_menu_page(){
	add_options_page(
		'Seo Us',
		'Seo Us',
		'edit_others_posts',
		'seous',
		'seous_page_admin'
	);
}

// Contenido de la pagina de administración
function seous_page_admin(){
	$text = '<div class="wrap">';
	$text .= '<h2>'.__('Documentación de Seo Us', 'seous' ).'</h2>';
	$text .= '<h3>'.__('Este plugin no es  la panacea', 'seous' ).'</h3>';
	$text .= '<p>'.__('Este plugin no es infalible solo nos ayudara a definir ciertas cosas para hacer SEO onpage mejor, para cualquier duda consulte con su SEO mas cercano.', 'seous' ).'</p>';
	$text .= '<p>'.__('Aqui podemos añadir todos los campor a completar si necesitamos opciones de configuración del plugin' ).'</p>';
	$text .= '<blockquote>'.__('A WordPress le gusta el SEO pero no hace SEO por ti' ).'</blockquote>';
	$text .= '</div>';
	echo $text;
}


/*_________________________________*\

			Limpiar HEAD
\*_________________________________*/

// Eliminamos WP version, feeds, generator, meta...

remove_action('wp_head', 'wp_generator');

// Eliminamos los hook del haed no necesarios en nuestro proyecto
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'feed_links', 2);
//remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
//remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

remove_action( 'wp_head', 'noindex', 1 );
// remove_action( 'wp_head', 'rel_canonical' );

/*_________________________________*\

			Añadir GATC
\*_________________________________*/
global $seous_config;

if ( ! $seous_config['gatc'] ){
	add_action( 'wp_head', 'seous_gatc_head' );
	function seous_gatc_head(){
		echo stripslashes( seous_gatc() );
	}
}

/*_________________________________*\

			Modificar Title
	necesario metabox.php
\*_________________________________*/

add_filter( 'wp_title', 'seous_wp_title', 10, 2 );
function seous_wp_title( $title, $sep ) {

	global $page, $paged, $seous_config, $post;

	// Add to title
	$title .= ' Estamos mejorando ';

	if ( is_feed() ) {
		return $title;
	}

	// Add the blog description for the home/front page.
	//$site_description = get_bloginfo( 'description', 'display' );
	if ( ( is_home() || is_front_page() ) ) {
		if ( $seous_config['general_title_site'] ){
			$title = $seous_config['general_title_site'];
		}
	}

	if  ( is_404() ) {
		if ( $seous_config['title_404'] ){
			$title = $seous_config['title_404'];
		}
	}

	if ( is_page() || is_single() ){
		$title_seo = get_post_meta( get_the_id(), '_seoustitle', true );
		if ( $title_seo ){
			$title = $title_seo;
		}
	}

	if ( is_search() ) {
		$title = $seous_config['title_search'];
	}

	if ( is_archive() ) {
		$title = $seous_config['title_archive'];
		/*
		condicional tags
		http://codex.wordpress.org/Conditional_Tags
			is_date()
			is_year()
			is_month()
			is_day()
			is_time()

			is_tax()
			is_category()
			is_tag()

			is_author()
		*/
	}

	// is_page_template() ;
	// is_sticky() // post pegajoso
	// is_new_day() // Post de Hoy

	// Add a page number if necessary:
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title .= " $sep " . sprintf( __( 'Page %s', '_s' ), max( $paged, $page ) );
	}

	return $title;
}




/*_________________________________*\

			Crear descripcion
		necesario metabox.php
\*_________________________________*/

function seous_description() {

	global $seous_config, $post;

	// Add the blog name
	if ( is_home() || is_front_page() ) {
		$description = $seous_config['seousdescription'] ;
	}

	if ( is_page() || is_single() ) {
		$seodescription = get_post_meta( get_the_id(), '_seousdescription', true );

		if ( ! $seodescription ){
			$seodescription = get_the_excerpt();
		}
		$description = $seodescription;
	}


	if ( ! $description  ) {
		$description = get_bloginfo('description');
	}
	echo '<meta name="description" content="'. $description . '" >';
}
add_action( 'wp_head', 'seous_description' );

/*_________________________________*\

			Meta robots
\*_________________________________*/

function seous_meta_robots() {
	$nofollow = get_post_meta( get_the_id(), '_seousrobots', true );
	if ( $nofollow ){
		echo '<meta name="robots" content="noindex, nofollow">';
	} else {
		if ( is_paged() || is_search() || is_archive() ) {
			echo '<meta name="robots" content="noindex, follow" >';

		} else {
			echo '<meta name="robots" content="index, follow">';
		}
	}
}
add_action( 'wp_head', 'seous_meta_robots' );


/*_________________________________*\

			Modificar robots
\*_________________________________*/

remove_action( 'do_robots', 'do_robots' );
add_action( 'do_robots', 'seous_robots');
function seous_robots() {
	header( 'Content-Type: text/plain; charset=utf-8' );

	// sitemap
	echo 'Sitemap: ' . home_url() . '/sitemap.xml';

	// spiders
	echo "User-agent: AhrefsBot\n";
	echo "Disallow: /\n";
	echo "User-agent: 008\n";
	echo "Disallow: /\n";
	echo "User-agent: JikeSpider\n";
	echo "Disallow: /\n";
	echo "User-agent: sistrix\n";
	echo "Disallow: /\n";
	echo "User-agent: TurnitinBot\n";
	echo "Disallow: /\n";
	echo "User-agent: MJ12bot\n";
	echo "Disallow: /\n";
	echo "User-agent: magpie-crawler\n";
	echo "Disallow: /\n";
	echo "User-agent: Flamingo_SearchEngine\n";
	echo "Disallow: /\n";
	echo "User-agent: Exabot\n";
	echo "Disallow: /\n";
	echo "User-agent: mag-crawl\n";
	echo "Disallow: /\n";
	echo "User-agent: magpie-crawler\n";
	echo "Disallow: /\n";
	echo "User-agent: Moreoverbot\n";
	echo "Disallow: /\n";
	echo "User-Agent: CyberAlert\n";
	echo "Disallow: /\n";
	echo "User-agent: Newscan\n";
	echo "Disallow: /\n";
	echo "User-agent: Spinn3r\n";
	echo "Disallow: /\n";

	// roots
	echo "User-agent: *\n";
	echo "Disallow: /cgi-bin\n";
	echo "Disallow: /wp-admin\n";
	echo "Disallow: /wp-includes\n";
	echo "Disallow: /wp-content/plugins\n";
	echo "Disallow: /plugins\n";
	echo "Disallow: /wp-content/cache\n";
	echo "Disallow: /wp-content/themes\n";
	echo "Disallow: /trackback\n";
	echo "Disallow: /feed\n";
	echo "Disallow: /comments\n";
	echo "Disallow: /category/*/*\n";
	echo "Disallow: */trackback\n";
	echo "Disallow: */feed\n";
	echo "Disallow: */comments\n";
	//echo "Disallow: /*?*\n";
	//echo "Disallow: /*?\n";
	//echo "Disallow: /js/\n";
	echo "Disallow: /politica-de-privacidad/\n";
	echo "Disallow: /formulario/\n";
	// echo "Allow: /wp-content/uploads\n";
	// echo "Allow: /assets\n";
	echo "\n";
}


/*_________________________________*\

			Modificar basic
\*_________________________________*/

add_action( 'wp_head', 'seous_head_basic' );
function seous_head_basic(){
	global $seous_config;

	echo '<meta content="' . $seous_config['distribution'] . '" name="distribution">';
	echo '<meta content="' . $seous_config['lang'].'" http-equiv="Content-Language">';
	echo '<meta content="' . $seous_config['lang'].'" name="lang">';
	echo '<meta content="' . $seous_config['locality'] . '" name="locality">';


	if ( $seous_config['autor_owner_copy'] ){
		echo '<meta name="author" content="'.$seous_config['autor_owner_copy'].'">';
		echo '<meta name="owner" content="'.$seous_config['autor_owner_copy'].'">';
		echo '<meta name="copyright" content="'.$seous_config['autor_owner_copy'].'">';
	}


	// revision
	if ( is_single( ) || is_page() ){
		if ( ! get_the_modified_date( 'm/d/Y' ) ){
			echo '<meta name="revised" content="'. get_the_modified_date( 'm/d/Y' ) .'">';
		}
	}
	// revisit-after
	if ( is_home() || is_front_page() ){
		if ( ! $seous_config['revisit'] ) {
			echo '<meta name="revisit-after" content="' . $seous_config['revisit'] . '">';
		}
	}
	// Browser configuration schema reference
	// http://msdn.microsoft.com/en-us/library/ie/dn320426%28v=vs.85%29.aspx
	echo '<meta name="msapplication-config" content="none">';

}



/*_________________________________*\

		site Verificacion
\*_________________________________*/

add_action( 'wp_head', 'seous_site_verify' );

function seous_site_verify(){
	global $seous_config;
	if ( is_home() || is_front_page() ){
		$alexaVerifyID 		= $seous_config['alexaVerifyID'];
		$msvalidate 		= $seous_config['msvalidate'];
		$googlesite 		= $seous_config['googlesite'];
		$pdomainverify 		= $seous_config['pdomainverify'];
		$yandexverification = $seous_config['yandexverification'];

		if (  ! $alexaVerifyID ){
			echo '<meta name="alexaVerifyID" content="' . $alexaVerifyID . '" >';
		}
		if (  ! $msvalidate ){
			echo '<meta name="msvalidate.01" content="' . $msvalidate. '" >';
		}
		if (  ! $googlesite ){
			echo '<meta name="google-site-verification" content="' . $googlesite . '" >';
		}
		if (  ! $pdomainverify ){
			echo '<meta name="p:domain_verify" content="' . $pdomainverify . '" >';
		}
		if (  ! $yandexverification ){
			echo '<meta name="yandex-verification" content="' . $yandexverification . '" >';
		}
	}
}


/*_________________________________*\

			more speed
\*_________________________________*/
add_filter('mod_rewrite_rules', 'seous_htaccess_contents');
function seous_htaccess_contents( $rules ) {
	$add_to_htaccess = "\n". '# SeoUs rules' . "\n";
	$add_to_htaccess .= '# Disable Etags'."\n";
	$add_to_htaccess .= 'Header unset ETag'."\n";
	$add_to_htaccess .= 'FileETag None'."\n";

	$add_to_htaccess .= "\n".'# Cache-Control'."\n";
	$add_to_htaccess .= '<IfModule mod_expires.c>'."\n";
	$add_to_htaccess .= '  ExpiresActive On'."\n";
	$add_to_htaccess .= '  # 1 YEAR'."\n";
	$add_to_htaccess .= '  <FilesMatch "\.(ico|pdf|flv)$">'."\n";
	$add_to_htaccess .= '  Header set Cache-Control "max-age=29030400, public"'."\n";
	$add_to_htaccess .= '  </FilesMatch>'."\n";
	$add_to_htaccess .= '  # 1 WEEK'."\n";
	$add_to_htaccess .= '  <FilesMatch "\.(jpg|jpeg|png|gif|swf)$">'."\n";
	$add_to_htaccess .= '  Header set Cache-Control "max-age=604800, public"'."\n";
	$add_to_htaccess .= '  </FilesMatch>'."\n";
	$add_to_htaccess .= '  # 2 DAYS'."\n";
	$add_to_htaccess .= '  <FilesMatch "\.(xml|txt|css|js)$">'."\n";
	$add_to_htaccess .= '  Header set Cache-Control "max-age=172800, proxy-revalidate"'."\n";
	$add_to_htaccess .= '  </FilesMatch>'."\n";
	$add_to_htaccess .= '  # 1 MIN'."\n";
	$add_to_htaccess .= '  <FilesMatch "\.(html|htm|php)$">'."\n";
	$add_to_htaccess .= '  Header set Cache-Control "max-age=60, private, proxy-revalidate"'."\n";
	$add_to_htaccess .= '  </FilesMatch>'."\n";
	$add_to_htaccess .= '</IfModule>'."\n";

	$add_to_htaccess .= "\n".'# Protect wpconfig.php'."\n";
	$add_to_htaccess .= '<Files wp-config.php>'."\n";
	$add_to_htaccess .= 'Order Allow,Deny'."\n";
	$add_to_htaccess .= 'Deny from all'."\n";
	$add_to_htaccess .= '</Files>'."\n";

	$add_to_htaccess .= "\n".'# Protect wpconfig.php'."\n";
	$add_to_htaccess .= 'redirect 301 /url_antigua.html http://www.dominio-nuevo.com/url-nueva/ '."\n";

    return $rules . $add_to_htaccess;
}




/*_________________________________*\

			social content
\*_________________________________*/

add_action( 'wp_head', 'seous_social_content' );

function seous_social_content(){
	global $seous_config, $post;

// Google Authorship and Publisher Markup
	echo '<link rel="author" href="https://plus.google.com/'. $seous_config['googleplus'] .'/posts" >';
	echo '<link rel="publisher" href="https://plus.google.com/'. $seous_config['googleplus_page'] .'" >';

/*_________________________________*\

			bola extra Nº1
		schema.org - Valido para  G+ & Pinterest
\*_________________________________ */
	if ( is_single( ) || is_page() ){
	// si es un articulo añadir
	// http://www.google.com/webmasters/tools/richsnippets
	// Schema.org markup for Google+

		echo '<meta itemscope itemtype="http://schema.org/Article">';
		echo '<meta itemprop="name" content="' . $seous_config['schemaname'] . '">';
		echo '<meta itemprop="description" content="' . $seous_config['schemadescription'] . '">';
		echo '<meta itemprop="image" content="'. $seous_config['schemaimage'] .'">';



	// Pinterest Rich Pins Validator - con incluir schema.org seria suficiente
	// http://developers.pinterest.com/rich_pins/validator/
/*_________________________________*\

			Twitter - https://dev.twitter.com/docs/cards
\*_________________________________*/
		echo '<meta name="twitter:card" content="summary_large_image">';
		echo '<meta name="twitter:site" content="' . $seous_config['twitter_site'] . '">';
		echo '<meta name="twitter:title" content="' . $seous_config['twitter_title'] . '">';
		echo '<meta name="twitter:description" content="' . $seous_config['twitter_description'] . '" >';
		echo '<meta name="twitter:creator" content="'. $seous_config['twitter_creator'] .'">';
		echo '<meta name="twitter:image:src" content="'. $seous_config['twitter_image_src'] .'">';
		echo '<meta name="twitter:url" content="'.get_permalink().'">';

/*_________________________________*\

			Facebook - http://ogp.me/
\*_________________________________*/
		echo '<meta property="og:title"  content="' . $seous_config['og_title'] . '">';
		echo '<meta property="og:description" content="'.get_bloginfo('og_description').'" >';
		echo '<meta property="og:type" content="website"/>';
		echo '<meta property="og:image" content="'. $seous_config['og_image'] .'">';
		echo '<meta property="og:url" content="'. get_permalink() .'">';
		echo '<meta property="og:site_name" content="'. $seous_config['og_site_name'] .'"/>';
		echo '<meta property="fb:admins" content="'. $seous_config['og_admins'] .'" />';
	}

}




/*_________________________________*\

			bola extra Nº2
			keywords para productos o medios
\*_________________________________*/



add_action( 'wp_head', 'seous_news_keywords' );

function seous_news_keywords(){
	global $seous_config;
	$keywords = '';

	if ( is_singular( $seous_config['pt_news'] ) ) {
		$posttags = get_the_tags();

		if ($posttags) {
			foreach($posttags as $tag) {
				$keywords .= $tag->name . ', ';
			}
		} else {
			// Listar las frases o palabras para ser encontrado
			$keywords .= $seous_config['news_keywords'];
		}
		echo '<meta name="news_keywords" content="' . $keywords . ' >';
	}


}



/*_________________________________*\

			bola extra Nº3
			Send mail if 404
\*_________________________________*/
	add_action( 'wp_footer', 'seous_404_mail' );


/*_________________________________*\

			bola extra Nº4
			Schema profile Author
\*_________________________________*/


function content_author_info( $content ){
	global $seous_config;
	$author_info = '<div itemtype="http://schema.org/Person" itemscope class="autor">';
	$author_info .= '<b>' . __('Autor: ','seous') . '</b>';
	$author_info .= '<a title="author profile" rel="author" itemprop="name" href="https://plus.google.com/'. $seous_config['googleplus_page'] .'">';
	$author_info .= get_the_author();
	$author_info .= '</a>. <br><b>' . __('Bio: ','seous') . '</b>';;
	$author_info .= '<span itemprop="description">' . get_the_author_meta('description') . '</span>';
	$author_info .= '</div>';

	$content = $content . $author_info;
	return $content;
}
add_filter( 'the_content', 'content_author_info' );


/*_________________________________*\

			Canonical
\*_________________________________*/


add_action( 'wp_head', 'seous_canonical' );
function seous_canonical(){
	global $post
	if ( is_paged() || is_search() || is_archive() ) {
		echo '<link rel="canonical" href="http://example.com/listado_de_las_mejores_casas" >';
	}
	if ( is_singular( 'casa' ) || is_single( ) ){
		return;
	}


	if ( is_page() ){
		$display_canonical = get_post_meta( get_the_id(), '_seousdiscanonical', true );
		if ( $display_canonical ){
			$url_canonical = get_post_meta( get_the_id(), '_seousurlcanonical', true );
			echo '<link rel="canonical" href="' . $url_canonical . '" >';
		}
	}
}

?>