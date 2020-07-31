<?php

namespace Jay\JPV;

/**
 * Plugin activator class
 */
class Activator {

    /**
     * Runs the activator
     *
     * @return void
     */
    public function run() {
        $this->add_info();
    }

    /**
     * Adds activation info
     *
     * @return void
     */
    public function add_info() {
        $activated = get_option( 'jpv_installed' );

        if ( ! $activated ) {
            update_option( 'jpv_installed', time() );
        }

        update_option( 'jpv_version', JPV_VERSION );
    }
}