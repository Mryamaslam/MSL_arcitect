<?php
/**
 * Architecture portfolio assets.
 *
 * @package Blocksy
 */

if (! defined('ABSPATH')) {
	exit;
}

add_action('wp_enqueue_scripts', 'blocksy_arch_enqueue_assets');

/**
 * Force MSL brand if WP still has old "Mohsin Shaheen Construction" title.
 */
add_filter('option_blogname', function ($name) {
	if (is_string($name) && (stripos($name, 'Mohsin') !== false || stripos($name, 'Shaheen') !== false)) {
		return 'MSL Interior';
	}
	return $name;
});

add_filter('option_blogdescription', function ($desc) {
	if (is_string($desc) && (stripos($desc, 'Creating The Difference') !== false || stripos($desc, 'Mohsin') !== false)) {
		return 'Modern · Smart · Luxury — your dream space, our expertise.';
	}
	return $desc;
});

add_filter('document_title_parts', function ($parts) {
	foreach (array('title', 'site') as $key) {
		if (! empty($parts[ $key ]) && (stripos($parts[ $key ], 'Mohsin') !== false || stripos($parts[ $key ], 'Shaheen') !== false)) {
			$parts[ $key ] = 'MSL Interior';
		}
	}
	return $parts;
});
/**
 * Disable Elementor / default theme footer on portfolio templates.
 */
add_action('template_redirect', function () {
	if (! (is_front_page() || is_singular('project') || is_post_type_archive('project'))) {
		return;
	}

	add_filter('elementor/theme/do_location', function ($do_location, $location) {
		if (in_array($location, ['footer', 'header'], true) && $location === 'footer') {
			return false;
		}

		if ($location === 'footer') {
			return false;
		}

		return $do_location;
	}, 20, 2);
});

function blocksy_arch_enqueue_assets() {
	$ver = '2.7.1';

	wp_enqueue_style(
		'blocksy-arch-portfolio',
		get_template_directory_uri() . '/inc/architecture-portfolio/portfolio.css',
		[],
		$ver
	);

	// Brand + nav contrast helpers (wins over Blocksy dynamic colors).
	$brand_css = ''
		. 'body.arch-portfolio-page #header .ct-container{width:100%!important;max-width:none!important;padding-left:clamp(1rem,3vw,2rem)!important;padding-right:clamp(1rem,3vw,2rem)!important}'
		. '.site-branding[data-id="logo"] .site-logo-container{display:flex!important;align-items:center!important}'
		. '.site-branding[data-id="logo"] .site-logo-container img{display:none!important;height:clamp(36px,4.2vw,48px)!important;width:auto!important;max-width:min(220px,42vw)!important;object-fit:contain!important}'
		. 'body.arch-nav-over-hero .site-branding[data-id="logo"] .default-logo{display:block!important}'
		. 'body.arch-nav-solid .site-branding[data-id="logo"] .sticky-logo,'
		. 'body.arch-portfolio-page #header [data-sticky*=yes] .site-branding[data-id="logo"] .sticky-logo{display:block!important}'
		. 'body.arch-nav-solid .site-branding[data-id="logo"] .default-logo,'
		. 'body.arch-portfolio-page #header [data-sticky*=yes] .site-branding[data-id="logo"] .default-logo{display:none!important}'
		. '.site-branding[data-id="logo"] .site-title-container{display:none!important}'
		. 'body.arch-nav-over-hero #header [data-id="menu"]>ul>li>a{color:#fff!important;-webkit-text-fill-color:#fff!important}'
		. 'body.arch-nav-solid #header [data-id="menu"]>ul>li>a,'
		. 'body.arch-portfolio-page #header [data-sticky*=yes] [data-id="menu"]>ul>li>a{color:#16181c!important;-webkit-text-fill-color:#16181c!important}';
	wp_add_inline_style('blocksy-arch-portfolio', $brand_css);

	if (is_front_page() || is_singular('project') || is_post_type_archive('project')) {
		wp_enqueue_script(
			'blocksy-arch-portfolio',
			get_template_directory_uri() . '/inc/architecture-portfolio/portfolio.js',
			[],
			$ver,
			true
		);
	}
}
