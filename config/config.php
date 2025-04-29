<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    // variable that are excluded during the creation of a new template using the model function
    'excluded' => ['id', 'created_at', 'updated_at', 'deleted_at'],
    
    // middleware for the analytics routes
    'middleware' => ['web', 'auth'],
    
    // analytics settings
    'analytics' => [
        'enabled' => true,
        'track_user_agent' => true,
        'track_ip_address' => true,
        'track_page_url' => true,
    ],
];
