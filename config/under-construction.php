<?php

return [

    /*
     * Activate under construction mode.
     */
    'enabled' => env('UNDER_CONSTRUCTION_ENABLED', true),

    /*
     * Hash for the current pin code
     */
    'hash' => env('UNDER_CONSTRUCTION_HASH', null),

    /*
     * Under construction title.
     */
    'title' => '出招吧骚年！',

    /*
     * Back button translation.
     */
    'back-button' => 'back',

    /*
    * Show button translation.
    */
    'show-button' => 'show',

    /*
     * Hide button translation.
     */
    'hide-button' => 'hide',

    /*
     * Show loader.
     */
    'show-loader' => true,

    /*
     * Redirect url after a successful login.
     */
    'redirect-url' => '/',

    /*
     * Enable throttle (max login attempts).
     */
    'throttle' => true,

        /*
        |--------------------------------------------------------------------------
        | Throttle settings (only when throttle is true)
        |--------------------------------------------------------------------------
        |
        */

        /*
        * Set the amount of digits (max 6).
        */
        'total_digits' => 6,

        /*
         * Set the maximum number of attempts to allow.
         */
        'max_attempts' => 3,

        /*
         * Show attempts left.
         */
        'show_attempts_left' => true,

        /*
         * Attempts left message.
         */
        'attempts_message' => '你还可以尝试: %i',

        /*
         * Too many attempts message.
         */
        'seconds_message' => '亲，请 %i 秒后再试一次吧.',

        /*
         * Set the number of minutes to disable login.
         */
        'decay_minutes' => 30,

        /*
         * Prevent the site from being indexed by Robots when locked
         */
        'lock_robots' => true,
];
