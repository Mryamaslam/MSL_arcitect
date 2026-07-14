<?php
/**
 * Front page — Architecture portfolio (readable multi-section layout).
 *
 * @package Blocksy
 */

add_filter('body_class', function ($classes) {
	$classes[] = 'arch-portfolio-page';
	$classes[] = 'arch-nav-over-hero';
	return $classes;
});

get_header();

$site_name = 'MSL Interior';
$wp_name   = trim((string) get_bloginfo('name'));
if ($wp_name && stripos($wp_name, 'Mohsin') === false && stripos($wp_name, 'Shaheen') === false) {
	$site_name = $wp_name;
}
$tagline = __('Modern · Smart · Luxury — your dream space, our expertise.', 'blocksy');
$wp_desc = trim((string) get_bloginfo('description'));
if ($wp_desc && stripos($wp_desc, 'Creating The Difference') === false && stripos($wp_desc, 'Mohsin') === false) {
	$tagline = $wp_desc;
}
$contact_url = home_url('/#contact');
$phone_primary   = '0312-0411414';
$phone_secondary = '0301-4491812';
$phone_primary_tel   = '+923120411414';
$phone_secondary_tel = '+923014491812';
$whatsapp_url = 'https://wa.me/923120411414';
$studio_address = __('Elite City, Kasur', 'blocksy');
$social_handle = 'MSL Interiors & Architects';
$facebook_url  = 'https://www.facebook.com/search/top/?q=MSL%20Interiors%20%26%20Architects';
$instagram_url = 'https://www.instagram.com/explore/search/keyword/?q=MSL%20Interiors%20Architects';

$hero_slides = [];
$hero_query = new WP_Query([
	'post_type'      => 'project',
	'posts_per_page' => 5,
	'post_status'    => 'publish',
	'meta_key'       => '_thumbnail_id',
	'orderby'        => 'date',
	'order'          => 'DESC',
]);

if ($hero_query->have_posts()) {
	while ($hero_query->have_posts()) {
		$hero_query->the_post();
		$url = get_the_post_thumbnail_url(get_the_ID(), 'full');
		if ($url) {
			$hero_slides[] = [
				'url'   => $url,
				'title' => get_the_title(),
			];
		}
	}
	wp_reset_postdata();
}

if (empty($hero_slides)) {
	$hero_slides = [
		['url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80', 'title' => ''],
		['url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80', 'title' => ''],
		['url' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80', 'title' => ''],
	];
}

$project_cats = get_terms([
	'taxonomy'   => 'project_category',
	'hide_empty' => true,
]);

// Prefer room-type filters from Images gallery (Lawn, Bedroom, Kitchen…).
$arch_room_order = ['bedroom', 'kitchen', 'washroom', 'living', 'lawn', 'exterior', 'model'];
if (! empty($project_cats) && ! is_wp_error($project_cats)) {
	usort($project_cats, function ($a, $b) use ($arch_room_order) {
		$ia = array_search($a->slug, $arch_room_order, true);
		$ib = array_search($b->slug, $arch_room_order, true);
		$ia = ($ia === false) ? 100 + $a->term_id : $ia;
		$ib = ($ib === false) ? 100 + $b->term_id : $ib;
		return $ia <=> $ib;
	});
}

$services = [
	[
		'num'   => '01',
		'title' => __('2D & 3D Drawings', 'blocksy'),
		'text'  => __('Accurate 2D plans and immersive 3D drawings so you can see every detail before work begins.', 'blocksy'),
	],
	[
		'num'   => '02',
		'title' => __('3D Elevations & Animations', 'blocksy'),
		'text'  => __('Photoreal elevations and walkthrough animations that bring your facade and interiors to life.', 'blocksy'),
	],
	[
		'num'   => '03',
		'title' => __('Interior & Exterior Designing', 'blocksy'),
		'text'  => __('Complete interior and exterior design — modern, smart, and luxury spaces tailored to your lifestyle.', 'blocksy'),
	],
	[
		'num'   => '04',
		'title' => __('Site Visits & Supervision', 'blocksy'),
		'text'  => __('On-site visits and site supervision so execution matches the approved design and quality.', 'blocksy'),
	],
	[
		'num'   => '05',
		'title' => __('Construction & Renovation', 'blocksy'),
		'text'  => __('New builds and renovations managed with clear coordination from concept to handover.', 'blocksy'),
	],
	[
		'num'   => '06',
		'title' => __('Cost & Material Estimation', 'blocksy'),
		'text'  => __('Transparent cost and material estimates so you plan budget, finishes, and scope with confidence.', 'blocksy'),
	],
	[
		'num'   => '07',
		'title' => __('Customized Furniture', 'blocksy'),
		'text'  => __('Bespoke furniture designed for your space — fitted, functional, and on-brand with the interiors.', 'blocksy'),
	],
	[
		'num'   => '08',
		'title' => __('Property Dealing & Consultancy', 'blocksy'),
		'text'  => __('Property dealing support and consultancy for smarter decisions on plots, homes, and investments.', 'blocksy'),
	],
	[
		'num'   => '09',
		'title' => __('Web Development', 'blocksy'),
		'text'  => __('Custom websites for your business — fast, mobile-friendly, and designed to showcase your brand online.', 'blocksy'),
	],
	[
		'num'   => '10',
		'title' => __('SEO', 'blocksy'),
		'text'  => __('Search engine optimization so clients can find you on Google — local reach, keywords, and better rankings.', 'blocksy'),
	],
];

$reviews = [
	[
		'quote' => __('MSL Interior brought clarity to our home. From drawings to interiors, everything felt thoughtful and well coordinated.', 'blocksy'),
		'name'  => 'Sara Khan',
		'role'  => __('Private Client · Kasur', 'blocksy'),
	],
	[
		'quote' => __('Their 3D elevations helped our family decide quickly. Site supervision kept quality consistent throughout.', 'blocksy'),
		'name'  => 'Omar Riaz',
		'role'  => __('Homeowner · Elite City', 'blocksy'),
	],
	[
		'quote' => __('Modern, smart, luxury — they delivered exactly that. Furniture and finishes were carefully matched to the design.', 'blocksy'),
		'name'  => 'Nadia Ahmed',
		'role'  => __('Residential Client', 'blocksy'),
	],
	[
		'quote' => __('Kitchen and washroom redesign was smooth from first visit to final handover. Communication stayed clear at every step.', 'blocksy'),
		'name'  => 'Amna Shah',
		'role'  => __('Renovation Client · Kasur', 'blocksy'),
	],
	[
		'quote' => __('We needed accurate drawings and cost estimates before construction. MSL Interior gave us both with practical options.', 'blocksy'),
		'name'  => 'Bilal Hassan',
		'role'  => __('Plot Owner · Lahore Road', 'blocksy'),
	],
	[
		'quote' => __('Professional team for exterior elevations and lawn planning. The visuals made it easy to approve the design early.', 'blocksy'),
		'name'  => 'Hassan Ali',
		'role'  => __('Villa Project · Elite City', 'blocksy'),
	],
	[
		'quote' => __('Custom furniture and interior styling were done together, so every room felt connected and complete.', 'blocksy'),
		'name'  => 'Fatima Noor',
		'role'  => __('Interior Client', 'blocksy'),
	],
];

$steps = [
	['title' => __('Listen & Survey', 'blocksy'), 'text' => __('We understand your needs, site, and budget — then map a clear design direction.', 'blocksy')],
	['title' => __('Design & Visualize', 'blocksy'), 'text' => __('2D/3D drawings, elevations, and interiors developed so you can review before build.', 'blocksy')],
	['title' => __('Estimate & Plan', 'blocksy'), 'text' => __('Cost and material estimates prepared with practical options for finishes and furniture.', 'blocksy')],
	['title' => __('Supervise & Deliver', 'blocksy'), 'text' => __('Site visits, supervision, and renovation support until your dream space is ready.', 'blocksy')],
];

$faqs = [
	[
		'q' => __('What services do you offer?', 'blocksy'),
		'a' => __('We provide 2D & 3D drawings, 3D elevations & animations, interior & exterior designing, site visits & supervision, construction & renovation, cost & material estimation, customized furniture, property dealing & consultancy, plus web development and SEO.', 'blocksy'),
	],
	[
		'q' => __('Do you offer a free consultation?', 'blocksy'),
		'a' => __('Yes. Call or WhatsApp us for a free consultation. Share your location, project type, and goals — we will guide the next steps.', 'blocksy'),
	],
	[
		'q' => __('Where are you based?', 'blocksy'),
		'a' => __('Our studio is in Elite City, Kasur. We also support projects with site visits and supervision as needed.', 'blocksy'),
	],
	[
		'q' => __('Can you handle both drawings and on-site work?', 'blocksy'),
		'a' => __('Yes. From drawings and elevations to construction, renovation, and site supervision — we stay involved through delivery.', 'blocksy'),
	],
	[
		'q' => __('Do you design custom furniture as well?', 'blocksy'),
		'a' => __('Yes. We design customized furniture that fits your plan, style, and space so interiors feel complete and intentional.', 'blocksy'),
	],
	[
		'q' => __('How can I contact MSL Interior?', 'blocksy'),
		'a' => __('Call 0312-0411414 or 0301-4491812, message us on WhatsApp, or reach us on Facebook & Instagram as MSL Interiors & Architects.', 'blocksy'),
	],
];
$hero_video_id  = (int) get_option('msl_hero_video_id');
$hero_video_url = $hero_video_id ? wp_get_attachment_url($hero_video_id) : '';
$hero_poster    = ! empty($hero_slides[0]['url']) ? $hero_slides[0]['url'] : '';
?>

<main class="arch-site" id="primary">

	<!-- HERO -->
	<section class="arch-hero<?php echo $hero_video_url ? ' arch-hero--video' : ''; ?>" data-arch-hero<?php echo $hero_video_url ? ' data-arch-hero-video' : ''; ?>>
		<div class="arch-hero__carousel" aria-hidden="true">
			<?php if ($hero_video_url) : ?>
				<video
					class="arch-hero__video"
					autoplay
					muted
					loop
					playsinline
					preload="metadata"
					<?php if ($hero_poster) : ?>poster="<?php echo esc_url($hero_poster); ?>"<?php endif; ?>
				>
					<source src="<?php echo esc_url($hero_video_url); ?>" type="video/mp4" />
				</video>
			<?php else : ?>
				<?php foreach ($hero_slides as $i => $slide) : ?>
					<div class="arch-hero__slide<?php echo 0 === $i ? ' is-active' : ''; ?>" style="background-image:url('<?php echo esc_url($slide['url']); ?>');"></div>
				<?php endforeach; ?>
			<?php endif; ?>
			<div class="arch-hero__shade"></div>
		</div>

		<div class="arch-wrap arch-hero__content">
			<p class="arch-kicker arch-kicker--on-dark"><?php esc_html_e('Modern · Smart · Luxury', 'blocksy'); ?></p>
			<h1 class="arch-hero__title"><?php echo esc_html($site_name); ?></h1>
			<p class="arch-hero__lead"><?php echo esc_html($tagline); ?></p>
			<div class="arch-hero__actions">
				<a class="arch-btn arch-btn--solid" href="#projects"><?php esc_html_e('View Projects', 'blocksy'); ?></a>
				<a class="arch-btn arch-btn--outline-light" href="#contact"><?php esc_html_e('Free Consultation', 'blocksy'); ?></a>
			</div>
		</div>

		<?php if (! $hero_video_url && count($hero_slides) > 1) : ?>
			<div class="arch-hero__controls">
				<button type="button" class="arch-hero__nav" data-arch-hero-prev aria-label="<?php esc_attr_e('Previous slide', 'blocksy'); ?>">‹</button>
				<div class="arch-hero__dots">
					<?php foreach ($hero_slides as $i => $slide) : ?>
						<button type="button" class="arch-hero__dot<?php echo 0 === $i ? ' is-active' : ''; ?>" data-arch-hero-dot="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr(sprintf(__('Slide %d', 'blocksy'), $i + 1)); ?>"></button>
					<?php endforeach; ?>
				</div>
				<button type="button" class="arch-hero__nav" data-arch-hero-next aria-label="<?php esc_attr_e('Next slide', 'blocksy'); ?>">›</button>
			</div>
		<?php endif; ?>
	</section>

	<!-- STATS -->
	<section class="arch-band arch-band--ink" aria-label="<?php esc_attr_e('Practice highlights', 'blocksy'); ?>">
		<div class="arch-wrap arch-stats">
			<div class="arch-stat">
				<strong>10</strong>
				<span><?php esc_html_e('Core services', 'blocksy'); ?></span>
			</div>
			<div class="arch-stat">
				<strong>2D/3D</strong>
				<span><?php esc_html_e('Drawings & animations', 'blocksy'); ?></span>
			</div>
			<div class="arch-stat">
				<strong>100%</strong>
				<span><?php esc_html_e('Site-focused delivery', 'blocksy'); ?></span>
			</div>
			<div class="arch-stat">
				<strong><?php esc_html_e('Free', 'blocksy'); ?></strong>
				<span><?php esc_html_e('Consultation available', 'blocksy'); ?></span>
			</div>
		</div>
	</section>

	<!-- ABOUT -->
	<section class="arch-section arch-section--paper" id="about">
		<div class="arch-wrap arch-split">
			<div class="arch-split__intro">
				<p class="arch-kicker"><?php esc_html_e('About MSL', 'blocksy'); ?></p>
				<h2 class="arch-h2"><?php esc_html_e('Your dream space — our expertise.', 'blocksy'); ?></h2>
			</div>
			<div class="arch-split__body">
				<p class="arch-lead">
					<?php esc_html_e('MSL Interiors & Architects designs modern, smart, and luxury spaces — from drawings and elevations to interiors, construction support, and custom furniture.', 'blocksy'); ?>
				</p>
				<p class="arch-text">
					<?php esc_html_e('Based in Elite City, Kasur, we guide clients from visualization to site supervision so the finished project matches the design intent.', 'blocksy'); ?>
				</p>
				<ul class="arch-checklist">
					<li><?php esc_html_e('Interior & exterior designing with 2D/3D visuals', 'blocksy'); ?></li>
					<li><?php esc_html_e('Construction, renovation & site supervision', 'blocksy'); ?></li>
					<li><?php esc_html_e('Cost estimation, furniture & property consultancy', 'blocksy'); ?></li>
				</ul>
				<a class="arch-link" href="#services"><?php esc_html_e('Explore services', 'blocksy'); ?> →</a>
			</div>
		</div>
	</section>

	<!-- SERVICES -->
	<section class="arch-section arch-section--mist" id="services">
		<div class="arch-wrap">
			<header class="arch-section__head">
				<div>
					<p class="arch-kicker"><?php esc_html_e('What we offer', 'blocksy'); ?></p>
					<h2 class="arch-h2"><?php esc_html_e('Services', 'blocksy'); ?></h2>
				</div>
				<p class="arch-section__aside">
					<?php esc_html_e('From drawings and elevations to interiors, furniture, consultancy, web development, and SEO — ten services under one team.', 'blocksy'); ?>
				</p>
			</header>

			<div class="arch-services">
				<?php foreach ($services as $service) : ?>
					<article class="arch-service">
						<span class="arch-service__num"><?php echo esc_html($service['num']); ?></span>
						<h3 class="arch-service__title"><?php echo esc_html($service['title']); ?></h3>
						<p class="arch-service__text"><?php echo esc_html($service['text']); ?></p>
						<a class="arch-link" href="#contact"><?php esc_html_e('Ask about this', 'blocksy'); ?> →</a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- PROCESS -->
	<section class="arch-section arch-section--ink" id="process">
		<div class="arch-wrap">
			<header class="arch-section__head arch-section__head--on-dark">
				<div>
					<p class="arch-kicker arch-kicker--on-dark"><?php esc_html_e('How we work', 'blocksy'); ?></p>
					<h2 class="arch-h2 arch-h2--on-dark"><?php esc_html_e('A clear process from brief to site', 'blocksy'); ?></h2>
				</div>
			</header>
			<ol class="arch-steps">
				<?php foreach ($steps as $i => $step) : ?>
					<li class="arch-step">
						<span class="arch-step__index"><?php echo esc_html(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
						<h3 class="arch-step__title"><?php echo esc_html($step['title']); ?></h3>
						<p class="arch-step__text"><?php echo esc_html($step['text']); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<!-- PROJECTS -->
	<section class="arch-section arch-section--paper arch-section--projects" id="projects">
		<div class="arch-wrap">
			<header class="arch-section__head">
				<div>
					<p class="arch-kicker"><?php esc_html_e('Portfolio', 'blocksy'); ?></p>
					<h2 class="arch-h2"><?php esc_html_e('Selected projects', 'blocksy'); ?></h2>
				</div>
				<p class="arch-section__aside">
					<?php esc_html_e('Filter by room — Bedroom, Kitchen, Washroom, Living, Lawn, and more.', 'blocksy'); ?>
				</p>
			</header>

			<?php if (! empty($project_cats) && ! is_wp_error($project_cats)) : ?>
				<div class="arch-filters" data-arch-filters role="tablist" aria-label="<?php esc_attr_e('Filter projects', 'blocksy'); ?>">
					<button type="button" class="arch-chip is-active" data-filter="all" role="tab" aria-selected="true"><?php esc_html_e('All', 'blocksy'); ?></button>
					<?php foreach ($project_cats as $term) : ?>
						<button type="button" class="arch-chip" data-filter="<?php echo esc_attr($term->slug); ?>" role="tab" aria-selected="false"><?php echo esc_html($term->name); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			$projects = new WP_Query([
				'post_type'      => 'project',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => 'menu_order date',
				'order'          => 'DESC',
			]);
			?>

			<?php if ($projects->have_posts()) : ?>
				<div class="arch-projects" data-arch-projects>
					<?php
					while ($projects->have_posts()) :
						$projects->the_post();
						$cats = get_the_terms(get_the_ID(), 'project_category');
						$cat_name = '';
						$cat_slugs = [];
						if ($cats && ! is_wp_error($cats)) {
							$cat_name = $cats[0]->name;
							foreach ($cats as $c) {
								$cat_slugs[] = $c->slug;
							}
						}
						?>
						<article class="arch-project" data-categories="<?php echo esc_attr(implode(' ', $cat_slugs)); ?>">
							<a class="arch-project__link" href="<?php the_permalink(); ?>">
								<span class="arch-project__media">
									<?php
									if (has_post_thumbnail()) {
										the_post_thumbnail('large', ['loading' => 'lazy']);
									} else {
										echo '<span class="arch-project__placeholder"></span>';
									}
									?>
									<span class="arch-project__veil" aria-hidden="true"></span>
								</span>
								<span class="arch-project__body">
									<?php if ($cat_name) : ?>
										<span class="arch-project__meta"><?php echo esc_html($cat_name); ?></span>
									<?php endif; ?>
									<span class="arch-project__title"><?php the_title(); ?></span>
									<span class="arch-project__cta"><?php esc_html_e('View project', 'blocksy'); ?> →</span>
								</span>
							</a>
						</article>
					<?php endwhile; ?>
				</div>
				<p class="arch-empty-note" data-arch-filter-empty hidden><?php esc_html_e('No projects in this category yet.', 'blocksy'); ?></p>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p class="arch-empty-note"><?php esc_html_e('Projects will appear here once published.', 'blocksy'); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<!-- REVIEWS -->
	<section class="arch-section arch-section--mist" id="reviews">
		<div class="arch-wrap">
			<header class="arch-section__head">
				<div>
					<p class="arch-kicker"><?php esc_html_e('Client voices', 'blocksy'); ?></p>
					<h2 class="arch-h2"><?php esc_html_e('Reviews', 'blocksy'); ?></h2>
				</div>
				<p class="arch-section__aside">
					<?php esc_html_e('Feedback from clients and collaborators who value precision and calm delivery.', 'blocksy'); ?>
				</p>
			</header>

			<div class="arch-reviews-marquee" data-arch-reviews>
				<div class="arch-reviews-viewport">
					<div class="arch-reviews-track">
						<?php
						// Duplicate set once so CSS marquee can loop without a gap.
						$review_loops = array_merge($reviews, $reviews);
						foreach ($review_loops as $ri => $review) :
							$is_clone = $ri >= count($reviews);
							?>
							<figure class="arch-review"<?php echo $is_clone ? ' aria-hidden="true"' : ''; ?>>
								<blockquote class="arch-review__quote">
									<p>“<?php echo esc_html($review['quote']); ?>”</p>
								</blockquote>
								<figcaption class="arch-review__who">
									<strong><?php echo esc_html($review['name']); ?></strong>
									<span><?php echo esc_html($review['role']); ?></span>
								</figcaption>
							</figure>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- FAQ -->
	<section class="arch-section arch-section--paper" id="faq">
		<div class="arch-wrap">
			<header class="arch-section__head">
				<div>
					<p class="arch-kicker"><?php esc_html_e('Common questions', 'blocksy'); ?></p>
					<h2 class="arch-h2"><?php esc_html_e('FAQ', 'blocksy'); ?></h2>
				</div>
				<p class="arch-section__aside">
					<?php esc_html_e('Quick answers about our services, process, and how we begin a project together.', 'blocksy'); ?>
				</p>
			</header>

			<div class="arch-faq" data-arch-faq>
				<?php foreach ($faqs as $i => $faq) : ?>
					<div class="arch-faq__item<?php echo 0 === $i ? ' is-open' : ''; ?>">
						<button
							type="button"
							class="arch-faq__question"
							aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>"
							aria-controls="arch-faq-panel-<?php echo (int) $i; ?>"
							id="arch-faq-btn-<?php echo (int) $i; ?>"
						>
							<span><?php echo esc_html($faq['q']); ?></span>
							<span class="arch-faq__icon" aria-hidden="true"></span>
						</button>
						<div
							class="arch-faq__answer"
							id="arch-faq-panel-<?php echo (int) $i; ?>"
							role="region"
							aria-labelledby="arch-faq-btn-<?php echo (int) $i; ?>"
							<?php echo 0 === $i ? '' : 'hidden'; ?>
						>
							<p><?php echo esc_html($faq['a']); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- CONTACT -->
	<section class="arch-section arch-section--mist" id="contact">
		<div class="arch-wrap arch-contact">
			<div class="arch-contact__info">
				<p class="arch-kicker"><?php esc_html_e('Free consultation', 'blocksy'); ?></p>
				<h2 class="arch-h2"><?php esc_html_e('Let’s design your dream space', 'blocksy'); ?></h2>
				<p class="arch-lead">
					<?php esc_html_e('Call, WhatsApp, or send a short inquiry. We help with drawings, interiors, construction, and property consultancy.', 'blocksy'); ?>
				</p>

				<dl class="arch-contact__list">
					<div>
						<dt><?php esc_html_e('Phone', 'blocksy'); ?></dt>
						<dd>
							<a href="<?php echo esc_url('tel:' . $phone_primary_tel); ?>"><?php echo esc_html($phone_primary); ?></a>
							<span class="arch-contact__sep"> / </span>
							<a href="<?php echo esc_url('tel:' . $phone_secondary_tel); ?>"><?php echo esc_html($phone_secondary); ?></a>
						</dd>
					</div>
					<div>
						<dt><?php esc_html_e('WhatsApp', 'blocksy'); ?></dt>
						<dd><a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($phone_primary); ?></a></dd>
					</div>
					<div>
						<dt><?php esc_html_e('Facebook & Instagram', 'blocksy'); ?></dt>
						<dd>
							<a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($social_handle); ?></a>
						</dd>
					</div>
					<div>
						<dt><?php esc_html_e('Studio', 'blocksy'); ?></dt>
						<dd><?php echo esc_html($studio_address); ?></dd>
					</div>
				</dl>
			</div>

			<form class="arch-form" id="arch-consult-form" action="<?php echo esc_url($whatsapp_url); ?>" method="get" data-arch-whatsapp="<?php echo esc_attr($whatsapp_url); ?>">
				<p class="arch-form__note"><?php esc_html_e('Tell us about your project — we will follow up on WhatsApp / call for a free consultation.', 'blocksy'); ?></p>
				<label class="arch-field">
					<span><?php esc_html_e('Full name *', 'blocksy'); ?></span>
					<input type="text" name="name" required placeholder="<?php esc_attr_e('Your name', 'blocksy'); ?>">
				</label>
				<label class="arch-field">
					<span><?php esc_html_e('Phone *', 'blocksy'); ?></span>
					<input type="tel" name="phone" required placeholder="03XX-XXXXXXX">
				</label>
				<label class="arch-field">
					<span><?php esc_html_e('Email', 'blocksy'); ?></span>
					<input type="email" name="email" placeholder="you@email.com">
				</label>
				<label class="arch-field">
					<span><?php esc_html_e('Project type', 'blocksy'); ?></span>
					<select name="type">
						<option value=""><?php esc_html_e('Select…', 'blocksy'); ?></option>
						<option value="2D / 3D Drawings"><?php esc_html_e('2D / 3D Drawings', 'blocksy'); ?></option>
						<option value="3D Elevations & Animations"><?php esc_html_e('3D Elevations & Animations', 'blocksy'); ?></option>
						<option value="Interior & Exterior Design"><?php esc_html_e('Interior & Exterior Design', 'blocksy'); ?></option>
						<option value="Construction & Renovation"><?php esc_html_e('Construction & Renovation', 'blocksy'); ?></option>
						<option value="Customized Furniture"><?php esc_html_e('Customized Furniture', 'blocksy'); ?></option>
						<option value="Property Consultancy"><?php esc_html_e('Property Consultancy', 'blocksy'); ?></option>
						<option value="Other"><?php esc_html_e('Other', 'blocksy'); ?></option>
					</select>
				</label>
				<label class="arch-field">
					<span><?php esc_html_e('Message *', 'blocksy'); ?></span>
					<textarea name="message" rows="5" required placeholder="<?php esc_attr_e('Location, project type, and what you need…', 'blocksy'); ?>"></textarea>
				</label>
				<button type="submit" class="arch-btn arch-btn--solid arch-btn--block"><?php esc_html_e('Request free consultation', 'blocksy'); ?></button>
				<p class="arch-form__fine">
					<?php
					printf(
						esc_html__('Or call us directly at %s', 'blocksy'),
						esc_html($phone_primary)
					);
					?>
				</p>
			</form>
		</div>
	</section>

	<!-- SITE FOOTER (replaces mismatched theme footer content visually) -->
	<footer class="arch-footer">
		<div class="arch-wrap arch-footer__inner">
			<div>
				<strong class="arch-footer__brand"><?php echo esc_html($site_name); ?></strong>
				<p><?php esc_html_e('Interiors & Architects · Modern · Smart · Luxury', 'blocksy'); ?></p>
				<p class="arch-footer__contact">
					<a href="<?php echo esc_url('tel:' . $phone_primary_tel); ?>"><?php echo esc_html($phone_primary); ?></a>
					<span class="arch-contact__sep"> · </span>
					<?php echo esc_html($studio_address); ?>
				</p>
			</div>
			<nav class="arch-footer__nav" aria-label="<?php esc_attr_e('Footer', 'blocksy'); ?>">
				<a href="#about"><?php esc_html_e('About', 'blocksy'); ?></a>
				<a href="#services"><?php esc_html_e('Services', 'blocksy'); ?></a>
				<a href="#projects"><?php esc_html_e('Projects', 'blocksy'); ?></a>
				<a href="#reviews"><?php esc_html_e('Reviews', 'blocksy'); ?></a>
				<a href="#faq"><?php esc_html_e('FAQ', 'blocksy'); ?></a>
				<a href="#contact"><?php esc_html_e('Contact', 'blocksy'); ?></a>
			</nav>
			<p class="arch-footer__copy">&copy; <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html($site_name); ?></p>
		</div>
	</footer>
</main>

<?php
get_footer();
