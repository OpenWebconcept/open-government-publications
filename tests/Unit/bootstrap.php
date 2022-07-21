<?php

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

/**
 * Bootstrap WordPress Mock.
 */
\WP_Mock::setUsePatchwork(true);
\WP_Mock::bootstrap();
