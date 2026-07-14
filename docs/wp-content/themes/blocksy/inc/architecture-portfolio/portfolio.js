/**
 * Hero carousel, project filters, FAQ + motion / scroll system.
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function ready(fn) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', fn);
		} else {
			fn();
		}
	}

	function initHeroCarousel() {
		var root = document.querySelector('[data-arch-hero]');
		if (!root || root.hasAttribute('data-arch-hero-video')) return;

		var slides = Array.prototype.slice.call(root.querySelectorAll('.arch-hero__slide'));
		if (slides.length < 2) return;

		var dots = Array.prototype.slice.call(root.querySelectorAll('[data-arch-hero-dot]'));
		var prev = root.querySelector('[data-arch-hero-prev]');
		var next = root.querySelector('[data-arch-hero-next]');
		var index = 0;
		var timer = null;

		function goTo(i) {
			index = (i + slides.length) % slides.length;
			slides.forEach(function (slide, n) {
				slide.classList.toggle('is-active', n === index);
			});
			dots.forEach(function (dot, n) {
				dot.classList.toggle('is-active', n === index);
			});
		}

		function start() {
			stop();
			timer = window.setInterval(function () {
				goTo(index + 1);
			}, 5500);
		}

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		if (prev) {
			prev.addEventListener('click', function () {
				goTo(index - 1);
				start();
			});
		}

		if (next) {
			next.addEventListener('click', function () {
				goTo(index + 1);
				start();
			});
		}

		dots.forEach(function (dot) {
			dot.addEventListener('click', function () {
				goTo(parseInt(dot.getAttribute('data-arch-hero-dot'), 10) || 0);
				start();
			});
		});

		root.addEventListener('mouseenter', stop);
		root.addEventListener('mouseleave', start);
		start();
	}

	function initProjectFilters() {
		var filters = document.querySelector('[data-arch-filters]');
		var grid = document.querySelector('[data-arch-projects]');
		if (!filters || !grid) return;

		var buttons = Array.prototype.slice.call(filters.querySelectorAll('[data-filter]'));
		var cards = Array.prototype.slice.call(grid.querySelectorAll('[data-categories]'));
		var empty = document.querySelector('[data-arch-filter-empty]');
		var animating = false;

		function applyFilter(filter) {
			var visible = 0;
			var toShow = [];
			var toHide = [];

			cards.forEach(function (card) {
				var cats = (card.getAttribute('data-categories') || '').split(/\s+/).filter(Boolean);
				var show = filter === 'all' || cats.indexOf(filter) !== -1;
				if (show) {
					visible += 1;
					toShow.push(card);
				} else {
					toHide.push(card);
				}
			});

			if (reduceMotion) {
				toHide.forEach(function (card) {
					card.classList.add('is-hidden');
					card.classList.remove('is-filtering-out', 'is-filtering-in');
				});
				toShow.forEach(function (card) {
					card.classList.remove('is-hidden', 'is-filtering-out', 'is-filtering-in');
				});
				if (empty) empty.hidden = visible > 0;
				return;
			}

			animating = true;
			toHide.forEach(function (card) {
				if (card.classList.contains('is-hidden')) return;
				card.classList.add('is-filtering-out');
			});

			window.setTimeout(function () {
				toHide.forEach(function (card) {
					card.classList.add('is-hidden');
					card.classList.remove('is-filtering-out', 'is-filtering-in');
				});

				toShow.forEach(function (card, i) {
					card.classList.remove('is-hidden');
					card.classList.remove('is-filtering-out');
					card.classList.add('is-filtering-in');
					card.style.setProperty('--arch-stagger', String(Math.min(i, 12) * 45) + 'ms');
					window.setTimeout(
						function () {
							card.classList.remove('is-filtering-in');
						},
						420 + Math.min(i, 12) * 45
					);
				});

				if (empty) empty.hidden = visible > 0;
				animating = false;
			}, 220);
		}

		buttons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				if (animating) return;
				var filter = btn.getAttribute('data-filter') || 'all';

				buttons.forEach(function (b) {
					var active = b === btn;
					b.classList.toggle('is-active', active);
					b.setAttribute('aria-selected', active ? 'true' : 'false');
				});

				applyFilter(filter);
			});
		});
	}

	function initFaqAccordion() {
		var root = document.querySelector('[data-arch-faq]');
		if (!root) return;

		var items = Array.prototype.slice.call(root.querySelectorAll('.arch-faq__item'));

		items.forEach(function (item) {
			var btn = item.querySelector('.arch-faq__question');
			var panel = item.querySelector('.arch-faq__answer');
			if (!btn || !panel) return;

			btn.addEventListener('click', function () {
				var isOpen = item.classList.contains('is-open');

				items.forEach(function (other) {
					other.classList.remove('is-open');
					var otherBtn = other.querySelector('.arch-faq__question');
					var otherPanel = other.querySelector('.arch-faq__answer');
					if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
					if (otherPanel) otherPanel.hidden = true;
				});

				if (!isOpen) {
					item.classList.add('is-open');
					btn.setAttribute('aria-expanded', 'true');
					panel.hidden = false;
				}
			});
		});
	}

	function initScrollReveal() {
		var site = document.querySelector('.arch-site, .arch-project-single');
		if (!site) return;

		site.classList.add('arch-motion');

		var selectors = [
			'.arch-section__head',
			'.arch-split__intro',
			'.arch-split__body',
			'.arch-service',
			'.arch-project',
			'.arch-step',
			'.arch-review',
			'.arch-faq__item',
			'.arch-contact__info',
			'.arch-form',
			'.arch-stat',
			'.arch-filters',
			'.arch-project-hero__content',
			'.arch-project-meta__item',
			'.arch-gallery__item',
		];

		var nodes = Array.prototype.slice.call(site.querySelectorAll(selectors.join(',')));
		nodes.forEach(function (el, i) {
			if (el.closest('.arch-hero')) return;
			el.classList.add('arch-reveal');
			if (!el.style.getPropertyValue('--arch-stagger')) {
				var siblingIndex = 0;
				var parent = el.parentElement;
				if (parent) {
					siblingIndex = Array.prototype.indexOf.call(parent.children, el);
				}
				el.style.setProperty('--arch-stagger', String(Math.min(siblingIndex, 8) * 70) + 'ms');
			}
		});

		if (reduceMotion) {
			nodes.forEach(function (el) {
				el.classList.add('is-revealed');
			});
			return;
		}

		if (!('IntersectionObserver' in window)) {
			nodes.forEach(function (el) {
				el.classList.add('is-revealed');
			});
			return;
		}

		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) return;
					entry.target.classList.add('is-revealed');
					io.unobserve(entry.target);
				});
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
		);

		nodes.forEach(function (el) {
			io.observe(el);
		});
	}

	function initScrollProgress() {
		var bar = document.createElement('div');
		bar.className = 'arch-scroll-progress';
		bar.setAttribute('aria-hidden', 'true');
		document.body.appendChild(bar);

		var ticking = false;
		function update() {
			ticking = false;
			var doc = document.documentElement;
			var max = Math.max(1, doc.scrollHeight - window.innerHeight);
			var p = Math.min(1, Math.max(0, window.scrollY / max));
			bar.style.transform = 'scaleX(' + p.toFixed(4) + ')';
		}

		window.addEventListener(
			'scroll',
			function () {
				if (ticking) return;
				ticking = true;
				window.requestAnimationFrame(update);
			},
			{ passive: true }
		);
		update();
	}

	function initProcessSpotlight() {
		var steps = Array.prototype.slice.call(document.querySelectorAll('.arch-step'));
		if (steps.length < 2 || !('IntersectionObserver' in window)) return;

		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) return;
					steps.forEach(function (s) {
						s.classList.toggle('is-spotlit', s === entry.target);
					});
				});
			},
			{ rootMargin: '-35% 0px -35% 0px', threshold: 0.2 }
		);

		steps.forEach(function (step) {
			io.observe(step);
		});
	}

	function initProjectTilt() {
		if (reduceMotion || window.matchMedia('(pointer: coarse)').matches) return;

		var cards = Array.prototype.slice.call(document.querySelectorAll('.arch-project'));
		cards.forEach(function (card) {
			var media = card.querySelector('.arch-project__media');
			var img = media ? media.querySelector('img') : null;
			if (!media) return;

			card.classList.add('arch-tilt');

			card.addEventListener('pointermove', function (e) {
				var rect = media.getBoundingClientRect();
				var px = (e.clientX - rect.left) / rect.width - 0.5;
				var py = (e.clientY - rect.top) / rect.height - 0.5;
				var rx = (-py * 9).toFixed(2);
				var ry = (px * 11).toFixed(2);
				media.style.transform =
					'perspective(1100px) rotateX(' + rx + 'deg) rotateY(' + ry + 'deg) translateZ(18px) scale(1.02)';
				media.style.setProperty('--arch-shine-x', ((px + 0.5) * 100).toFixed(1) + '%');
				media.style.setProperty('--arch-shine-y', ((py + 0.5) * 100).toFixed(1) + '%');
				if (img) {
					img.style.transform =
						'scale(1.12) translate3d(' + (px * 10).toFixed(1) + 'px,' + (py * 8).toFixed(1) + 'px,0)';
				}
			});

			card.addEventListener('pointerleave', function () {
				media.style.transform = '';
				if (img) img.style.transform = '';
			});
		});
	}

	function initHeroParallax() {
		var hero = document.querySelector('[data-arch-hero]');
		if (!hero) return;

		hero.classList.add('arch-hero--ready');

		if (reduceMotion) return;

		var carousel = hero.querySelector('.arch-hero__carousel');
		var content = hero.querySelector('.arch-hero__content');
		var shade = hero.querySelector('.arch-hero__shade');
		var ticking = false;

		function update() {
			ticking = false;
			var rect = hero.getBoundingClientRect();
			var h = rect.height || 1;
			var progress = Math.min(1, Math.max(0, -rect.top / h));
			var y = progress * 48;
			if (carousel) carousel.style.transform = 'translate3d(0,' + y * 0.55 + 'px,0) scale(' + (1 + progress * 0.04) + ')';
			if (shade) shade.style.transform = 'translate3d(0,' + y * 0.35 + 'px,0)';
			if (content) content.style.transform = 'translate3d(0,' + y * 0.7 + 'px,0)';
		}

		window.addEventListener(
			'scroll',
			function () {
				if (ticking) return;
				ticking = true;
				window.requestAnimationFrame(update);
			},
			{ passive: true }
		);

		update();
	}

	function initConsultForm() {
		var form = document.getElementById('arch-consult-form');
		if (!form) return;

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var base = form.getAttribute('data-arch-whatsapp') || form.getAttribute('action') || '';
			if (!base) return;

			var name = (form.querySelector('[name="name"]') || {}).value || '';
			var phone = (form.querySelector('[name="phone"]') || {}).value || '';
			var email = (form.querySelector('[name="email"]') || {}).value || '';
			var type = (form.querySelector('[name="type"]') || {}).value || '';
			var message = (form.querySelector('[name="message"]') || {}).value || '';

			var lines = [
				'Assalam o Alaikum MSL Interior,',
				'Free consultation request:',
				'Name: ' + name,
				'Phone: ' + phone,
			];
			if (email) lines.push('Email: ' + email);
			if (type) lines.push('Project: ' + type);
			lines.push('Message: ' + message);

			var url = base.split('?')[0] + '?text=' + encodeURIComponent(lines.join('\n'));
			window.open(url, '_blank', 'noopener');
		});
	}

	function initStatCounters() {
		if (reduceMotion) return;

		var stats = Array.prototype.slice.call(document.querySelectorAll('.arch-stat strong'));
		if (!stats.length || !('IntersectionObserver' in window)) return;

		function parseTarget(text) {
			var clean = String(text || '').trim();
			var match = clean.match(/^(\d+(?:\.\d+)?)(.*)$/);
			if (!match) return null;
			return { value: parseFloat(match[1]), suffix: match[2] || '' };
		}

		function animateValue(el, target, suffix) {
			var start = null;
			var duration = 1100;
			function frame(ts) {
				if (start === null) start = ts;
				var t = Math.min(1, (ts - start) / duration);
				var eased = 1 - Math.pow(1 - t, 3);
				var current = Math.round(target * eased);
				el.textContent = String(current) + suffix;
				if (t < 1) window.requestAnimationFrame(frame);
			}
			window.requestAnimationFrame(frame);
		}

		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) return;
					var el = entry.target;
					var parsed = parseTarget(el.getAttribute('data-arch-count') || el.textContent);
					if (parsed) {
						el.setAttribute('data-arch-count', el.textContent);
						animateValue(el, parsed.value, parsed.suffix);
					}
					io.unobserve(el);
				});
			},
			{ threshold: 0.4 }
		);

		stats.forEach(function (el) {
			var raw = el.textContent.trim();
			el.setAttribute('data-arch-count', raw);
			if (parseTarget(raw)) {
				io.observe(el);
			}
		});
	}

	function initSmoothAnchors() {
		document.querySelectorAll('a[href^="#"]').forEach(function (link) {
			link.addEventListener('click', function (e) {
				var id = link.getAttribute('href');
				if (!id || id === '#') return;
				var target = document.querySelector(id);
				if (!target) return;
				e.preventDefault();
				target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
			});
		});
	}

	function initNavContrast() {
		var body = document.body;
		if (!body.classList.contains('arch-portfolio-page')) {
			if (!document.querySelector('.arch-site')) return;
			body.classList.add('arch-portfolio-page');
		}

		var hero = document.querySelector('.arch-hero');

		function update() {
			var solid = true;
			if (hero) {
				var rect = hero.getBoundingClientRect();
				solid = rect.bottom <= 80;
			} else {
				solid = window.scrollY > 40;
			}
			body.classList.toggle('arch-nav-solid', solid);
			body.classList.toggle('arch-nav-over-hero', !solid);
		}

		update();
		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
	}

	function initMagneticButtons() {
		if (reduceMotion || window.matchMedia('(pointer: coarse)').matches) return;

		var buttons = Array.prototype.slice.call(document.querySelectorAll('.arch-btn'));
		buttons.forEach(function (btn) {
			btn.classList.add('arch-btn--magnetic');

			btn.addEventListener('pointermove', function (e) {
				var rect = btn.getBoundingClientRect();
				var x = e.clientX - rect.left;
				var y = e.clientY - rect.top;
				btn.style.setProperty('--mx', ((x / rect.width) * 100).toFixed(1) + '%');
				btn.style.setProperty('--my', ((y / rect.height) * 100).toFixed(1) + '%');
				var dx = (x - rect.width / 2) / 8;
				var dy = (y - rect.height / 2) / 8;
				btn.style.transform = 'translate3d(' + dx.toFixed(1) + 'px,' + dy.toFixed(1) + 'px,0)';
			});

			btn.addEventListener('pointerleave', function () {
				btn.style.transform = '';
			});
		});
	}

	ready(function () {
		document.documentElement.classList.add('arch-js');
		initHeroCarousel();
		initProjectFilters();
		initFaqAccordion();
		initScrollReveal();
		initHeroParallax();
		initProjectTilt();
		initStatCounters();
		initSmoothAnchors();
		initMagneticButtons();
		initScrollProgress();
		initProcessSpotlight();
		initConsultForm();
		initNavContrast();
	});
})();
