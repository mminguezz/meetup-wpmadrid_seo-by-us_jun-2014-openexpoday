<?php
/**
 * Plugin Name: SeoUs
 * Info: Metabox para recoger el titulo y la descripcion de las paginas
 *
 **/



// ver info: http://codex.wordpress.org/Function_Reference/add_meta_box
function seous_meta_box(){
	global $seous_config;

	$define_pt_for_title_and_desc = $seous_config['define_pt_for_title_and_desc'];

	if( ! $define_pt_for_title_and_desc ){
		// Buscamos donde queremos añadir nuestros metabox
		$args = array(
			'public'   => true,
			'_builtin' => false
		);// solo los custom-post-types,  excluye post, page, attachment
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types_custom = get_post_types( $args, $output, $operator );
		// Creamos un array con los tipos de post donde necesitamos añadir metabox
		$post_types_default = array( 'post','page');
		$post_types_for_cmb = array_merge( $post_types_default, $post_types_custom );
		$define_pt_for_title_and_desc = $post_types_for_cmb;
	}
	foreach ( $define_pt_for_title_and_desc as $post_type ) {
		add_meta_box('seous-meta',__( 'Meta tags' , 'seous' ), 'seousmeta_fields', $post_type );
	}
}

add_action('add_meta_boxes','seous_meta_box', 20);

function seousmeta_fields( $post ){
	//user nonce for verirfication
	wp_nonce_field( 'seous_meta_box' , 'seous_nonce' );

	$seotitle = get_post_meta( $post->ID, '_seoustitle', true );
	// Input meta title
	$fields  = '<label for="seoustitle"><strong>' . __('Title','seous') . '</strong></label><br>';
	$fields .= '<input type="text" id="seoustitle" name="seoustitle" value="' . esc_attr( $seotitle ) . '" size="25"><br><br>';

	echo $fields;

	$seodescription = get_post_meta( $post->ID, '_seousdescription', true );
	// Input meta description
	$fields  = '<label for="seousdescription"><strong>' . __('Description','seous') . '</strong></label><br>';
	$fields .= '<textarea rows="4" cols="50" id="seousdescription" name="seousdescription" >' . esc_attr( $seodescription ) . '</textarea><br><br>';

	echo $fields;

	$seousrobots = get_post_meta( $post->ID, '_seousrobots', true );
	// Input meta description
	$fields  = '<label for="seousrobots"><strong>' . __('Meta noindex nofollow','seous') . '</strong></label><br>';
	$fields .= '<input type="checkbox" id="seousrobots" name="seousrobots" value="1" '. checked( $seousrobots, 1, false ) .'><br><br>';

	echo $fields;
	if ( is_page() ) {
		$seouscanonical = get_post_meta( $post->ID, '_seousurlcanonical', true );
		// Input meta description
		$fields  = '<label for="seousurlcanonical"><strong>' . __('Meta noindex nofollow','seous') . '</strong></label><br>';
		$fields .= '<input type="text" id="seousurlcanonical" name="seousurlcanonical" value="' . esc_attr( $seotitle ) . '" size="25"><br><br>';

		echo $fields;
	}





}
function seousmeta_save_features( $post_id ){
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	// Make sure that it is set.
	if ( ! isset( $_POST['seous_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['seous_nonce'], 'seous_meta_box' ) ) {
		return;
	}

	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )  {
		return;
	}

	// Check permissions
	if ( ! current_user_can( 'edit_others_posts', $post_id ) ){
		return;
	}


	// OK, we're authenticated: we need to find and save the data

	$seoustitle_text = sanitize_text_field( $_POST['seoustitle'] );
	update_post_meta( $post_id , '_seoustitle' , $seoustitle_text);

	$seousdescription_text = sanitize_text_field( $_POST['seousdescription'] );
	update_post_meta( $post_id , '_seousdescription' , $seousdescription_text);

	$seousrobots_text = sanitize_text_field( $_POST['seousrobots'] );
	update_post_meta( $post_id , '_seousrobots' , $seousrobots_text);

	if ( is_page() ){
		$seousurlcanonical_text = sanitize_text_field( $_POST['seousurlcanonical'] );
		update_post_meta( $post_id , '_seousurlcanonical' , $seousurlcanonical_text);
	}

}
//when post is saved
add_action('save_post', 'seousmeta_save_features');


?>
