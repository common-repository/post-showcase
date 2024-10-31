<?php
/**
 * Plugin Name:       Post Showcase
 * Plugin URI:        https://beautifulplugins.com/post-showcase/
 * Description:       Post Showcase is a powerful and user-friendly WordPress plugin that allows you to display your posts in a grid layout.
 * Version:           1.1.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            BeautifulPlugins
 * Author URI:        https://beautifulplugins.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-showcase
 * Domain Path:       /languages
 *
 * @package PostShowcase
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

use PostShowcase\Plugin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Optimized autoload classes.
 *
 * @since 1.0.0
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return Plugin
 */
function post_showcase() {
	return Plugin::create( __FILE__, '1.1.0' );
}

// Initialize the plugin.
post_showcase();
