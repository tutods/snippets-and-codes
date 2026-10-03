<?php

remove_action( 'wp_head', 'wp_generator' );

add_filter( 'the_generator', '__return_empty_string' );
