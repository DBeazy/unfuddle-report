<?php

// Load all of the php files in middleware
foreach (glob(APP_PATH . 'app/middleware/*.php') as $filename) {
    include_once $filename;
}
