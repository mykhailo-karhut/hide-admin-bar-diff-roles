<?php 
    // die if not uninstalling
    if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
        exit ();

        delete_option( 'mkwp_options_admin_bar' );

?>