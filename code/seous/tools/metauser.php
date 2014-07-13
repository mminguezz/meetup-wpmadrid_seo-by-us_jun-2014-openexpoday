<?php
/**
 * Plugin Name: SeoUs
 * Info: Añade campos para personalizar usuarios
 *
 **/

add_filter('user_contactmethods','seous_contactmethods',10,1);
function seous_contactmethods( $contactmethods ) {
	$contactmethods['twitter'] = __('Twitter', 'seous' ); // Add Twitter
	$contactmethods['facebook'] = __('Facebook', 'seous' ); // Add Facebook
	$contactmethods['googleplus'] = __('Google +', 'seous' ); // Add google + profile
	$contactmethods['googleplus_page'] = __('Google + page', 'seous' ); // Add google + page profile
	//$contactmethods['skype'] = __('Skype Username', TEXT_DOMINE ); // Add skype
	unset($contactmethods['yim']); // Remove Yahoo IM
	unset($contactmethods['aim']); // Remove AIM
	unset($contactmethods['jabber']); // Remove Jabber
	return $contactmethods;
}


?>
