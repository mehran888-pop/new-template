/*!
 * Novin AI — اسکریپت جلوه‌های سه‌بعدی و تعاملی
 *
 * تمام افکت‌ها از طریق سفارشی‌ساز وردپرس (بخش «افکت‌های 3D و فراگیر»)
 * قابل فعال یا غیرفعال کردن هستند و به تنظیم «کاهش حرکت» مرورگر احترام
 * می‌گذارند.
 */
(function () {
	'use strict';

	var settings = window.NovinAiSettings || {};
	var reduceMotion =
		settings.reducedMotion &&
		window.matchMedia &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function $(selector, scope) {
		return (scope || document).querySelector(selector);
	}

	function $$(selector, scope) {
		return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
	}

	function on(target, event, handler, options) {
		if (target) {
			target.addEventListener(event, handler, options || false);
		}
	}

	var isRtl = document.documentElement.getAttribute('dir') === 'rtl';

	/* ------------------------------------------------------------------
	 * هدر چسبان
	 * ------------------------------------------------------------------ */
	function initStickyHeader() {
		var headers = $$('[data-nv-header], .nv-header.is-sticky, .nv-header--fallback');

		if (!headers.length) {
			return;
		}

		function update() {
			var scrolled = window.pageYOffset > 40;

			headers.forEach(function (header) {
				header.classList.toggle('is-scrolled', scrolled);
			});
		}

		on(window, 'scroll', update, { passive: true });
		update();
	}

	/* ------------------------------------------------------------------
	 * منوی آف‌کنوس
	 * ------------------------------------------------------------------ */
	function initOffcanvas() {
		function close(panel) {
			panel.classList.remove('is-open');
			panel.setAttribute('aria-hidden', 'true');

			var trigger = $('[data-nv-offcanvas-open="' + panel.id + '"]');

			if (trigger) {
				trigger.setAttribute('aria-expanded', 'false');
			}
		}

		function open(panel) {
			panel.classList.add('is-open');
			panel.setAttribute('aria-hidden', 'false');

			var trigger = $('[data-nv-offcanvas-open="' + panel.id + '"]');

			if (trigger) {
				trigger.setAttribute('aria-expanded', 'true');
			}
		}

		$$('[data-nv-offcanvas-open]').forEach(function (button) {
			on(button, 'click', function (event) {
				event.preventDefault();

				var panel = document.getElementById(button.getAttribute('data-nv-offcanvas-open'));

				if (panel) {
					open(panel);
				}
			});
		});

		$$('[data-nv-offcanvas-close]').forEach(function (button) {
			on(button, 'click', function (event) {
				event.preventDefault();

				var panel = button.closest('.nv-offcanvas');

				if (panel) {
					close(panel);
				}
			});
		});

		on(document, 'keydown', function (event) {
			if ('Escape' === event.key) {
				$$('.nv-offcanvas.is-open').forEach(close);
				$$('.nv-lightbox').forEach(function (box) {
					box.remove();
				});
			}
		});
	}

	/* ------------------------------------------------------------------
	 * پنل جستجو
	 * ------------------------------------------------------------------ */
	function initSearchToggle() {
		$$('[data-nv-toggle]').forEach(function (button) {
			on(button, 'click', function (event) {
				event.preventDefault();

				var panel = document.getElementById(button.getAttribute('data-nv-toggle'));

				if (!panel) {
					return;
				}

				var isOpen = panel.classList.toggle('is-open');

				if (isOpen) {
					var input = $('input[type="search"]', panel);

					if (input) {
						input.focus();
					}
				}
			});
		});
	}

	/* ------------------------------------------------------------------
	 * چرخش سه‌بعدی کارت‌ها هنگام حرکت موس
	 * ------------------------------------------------------------------ */
	function initTilt() {
		if (!settings.tilt || reduceMotion) {
			return;
		}

		if (window.matchMedia && window.matchMedia('(hover: none)').matches) {
			return;
		}

		var strength = parseFloat(settings.tiltStrength) || 12;

		$$('.nv-tilt').forEach(function (el) {
			var rect = null;

			function refresh() {
				rect = el.getBoundingClientRect();
			}

			on(el, 'mouseenter', function () {
				refresh();
				el.classList.add('is-tilting');
			});

			on(el, 'mousemove', function (event) {
				if (!rect) {
					refresh();
				}

				var px = (event.clientX - rect.left) / rect.width - 0.5;
				var py = (event.clientY - rect.top) / rect.height - 0.5;
				var ry = (isRtl ? -px : px) * strength;
				var rx = -py * strength;

				el.style.setProperty('--nv-ry', ry.toFixed(2) + 'deg');
				el.style.setProperty('--nv-rx', rx.toFixed(2) + 'deg');

				// درخشش متحرک روی کارت
				el.style.setProperty('--nv-mx', ((px + 0.5) * 100).toFixed(1) + '%');
				el.style.setProperty('--nv-my', ((py + 0.5) * 100).toFixed(1) + '%');
			});

			on(el, 'mouseleave', function () {
				el.classList.remove('is-tilting');
				el.style.setProperty('--nv-rx', '0deg');
				el.style.setProperty('--nv-ry', '0deg');
			});
		});
	}

	/* ------------------------------------------------------------------
	 * دکمه‌های مغناطیسی
	 * ------------------------------------------------------------------ */
	function initMagnetic() {
		if (!settings.magnetic || reduceMotion) {
			return;
		}

		if (window.matchMedia && window.matchMedia('(hover: none)').matches) {
			return;
		}

		$$('.nv-magnetic').forEach(function (el) {
			on(el, 'mousemove', function (event) {
				var rect = el.getBoundingClientRect();
				var x = (event.clientX - rect.left) / rect.width - 0.5;
				var y = (event.clientY - rect.top) / rect.height - 0.5;

				el.style.transform =
					'translate3d(' +
					((isRtl ? -x : x) * 14).toFixed(2) +
					'px, ' +
					(y * 10).toFixed(2) +
					'px, 0)';
			});

			on(el, 'mouseleave', function () {
				el.style.transform = '';
			});
		});
	}

	/* ------------------------------------------------------------------
	 * ظهور تدریجی هنگام اسکرول
	 * ------------------------------------------------------------------ */
	function initReveal() {
		var items = $$('.nv-reveal');

		if (!items.length) {
			return;
		}

		if (reduceMotion || !('IntersectionObserver' in window)) {
			items.forEach(function (item) {
				item.classList.add('is-visible');
			});

			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						var el = entry.target;
						var delay = el.getAttribute('data-nv-delay');

						if (delay) {
							el.style.transitionDelay = delay + 'ms';
						}

						el.classList.add('is-visible');
						observer.unobserve(el);
					}
				});
			},
			{ threshold: 0.12, rootMargin: '0px 0px -60px 0px' }
		);

		items.forEach(function (item) {
			observer.observe(item);
		});
	}

	/* ------------------------------------------------------------------
	 * شمارنده اعداد
	 * ------------------------------------------------------------------ */
	function initCounters() {
		var counters = $$('.nv-counter');

		if (!counters.length) {
			return;
		}

		function animate(el) {
			var target = parseFloat(el.getAttribute('data-nv-count')) || 0;
			var duration = 1600;
			var start = null;

			if (reduceMotion) {
				el.textContent = String(target);
				return;
			}

			function step(timestamp) {
				if (!start) {
					start = timestamp;
				}

				var progress = Math.min((timestamp - start) / duration, 1);
				var eased = 1 - Math.pow(1 - progress, 3);

				el.textContent = String(Math.round(target * eased));

				if (progress < 1) {
					window.requestAnimationFrame(step);
				} else {
					el.textContent = String(target);
				}
			}

			window.requestAnimationFrame(step);
		}

		if (!('IntersectionObserver' in window)) {
			counters.forEach(animate);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						animate(entry.target);
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.4 }
		);

		counters.forEach(function (counter) {
			observer.observe(counter);
		});
	}

	/* ------------------------------------------------------------------
	 * ذرات هوشمند در پس‌زمینه
	 * ------------------------------------------------------------------ */
	function initParticles() {
		if (!settings.particles || reduceMotion) {
			return;
		}

		var canvases = $$('canvas[data-nv-particles], canvas.nv-particles');

		canvases.forEach(function (canvas) {
			var ctx = canvas.getContext('2d');

			if (!ctx) {
				return;
			}

			var width = 0;
			var height = 0;
			var nodes = [];
			var raf = null;
			var visible = true;

			function resize() {
				var rect = canvas.getBoundingClientRect();
				var dpr = window.devicePixelRatio || 1;

				width = rect.width || canvas.offsetWidth;
				height = rect.height || canvas.offsetHeight;

				canvas.width = width * dpr;
				canvas.height = height * dpr;
				ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

				var count = Math.min(90, Math.round((width * height) / 16000));

				nodes = [];

				for (var i = 0; i < count; i++) {
					nodes.push({
						x: Math.random() * width,
						y: Math.random() * height,
						vx: (Math.random() - 0.5) * 0.32,
						vy: (Math.random() - 0.5) * 0.32,
						r: Math.random() * 1.7 + 0.6
					});
				}
			}

			function draw() {
				if (!visible) {
					raf = null;
					return;
				}

				ctx.clearRect(0, 0, width, height);

				var primary = getComputedStyle(document.documentElement)
					.getPropertyValue('--nv-primary')
					.trim() || '#6d5efc';

				for (var i = 0; i < nodes.length; i++) {
					var node = nodes[i];

					node.x += node.vx;
					node.y += node.vy;

					if (node.x < 0 || node.x > width) {
						node.vx *= -1;
					}

					if (node.y < 0 || node.y > height) {
						node.vy *= -1;
					}

					ctx.beginPath();
					ctx.arc(node.x, node.y, node.r, 0, Math.PI * 2);
					ctx.fillStyle = 'rgba(160, 150, 255, 0.75)';
					ctx.fill();

					for (var j = i + 1; j < nodes.length; j++) {
						var other = nodes[j];
						var dx = node.x - other.x;
						var dy = node.y - other.y;
						var dist = Math.sqrt(dx * dx + dy * dy);

						if (dist < 130) {
							ctx.beginPath();
							ctx.strokeStyle = 'rgba(139, 124, 255, ' + (0.22 * (1 - dist / 130)).toFixed(3) + ')';
							ctx.lineWidth = 1;
							ctx.moveTo(node.x, node.y);
							ctx.lineTo(other.x, other.y);
							ctx.stroke();
						}
					}
				}

				raf = window.requestAnimationFrame(draw);
			}

			resize();
			draw();

			on(window, 'resize', function () {
				resize();
			});

			if ('IntersectionObserver' in window) {
				var observer = new IntersectionObserver(function (entries) {
					entries.forEach(function (entry) {
						visible = entry.isIntersecting;

						if (visible && !raf) {
							draw();
						}
					});
				});

				observer.observe(canvas);
			}
		});
	}

	/* ------------------------------------------------------------------
	 * هاله نوری دنبال‌کننده موس
	 * ------------------------------------------------------------------ */
	function initCursorGlow() {
		if (!settings.cursor || reduceMotion) {
			return;
		}

		if (window.matchMedia && window.matchMedia('(hover: none)').matches) {
			return;
		}

		var glow = document.createElement('div');

		glow.className = 'nv-cursor-glow';
		document.body.appendChild(glow);

		var x = 0;
		var y = 0;
		var currentX = 0;
		var currentY = 0;

		on(
			document,
			'mousemove',
			function (event) {
				x = event.clientX;
				y = event.clientY;
			},
			{ passive: true }
		);

		(function loop() {
			currentX += (x - currentX) * 0.12;
			currentY += (y - currentY) * 0.12;

			glow.style.transform =
				'translate3d(' + currentX.toFixed(1) + 'px, ' + currentY.toFixed(1) + 'px, 0)';

			window.requestAnimationFrame(loop);
		})();
	}

	/* ------------------------------------------------------------------
	 * آکاردئون سوالات متداول
	 * ------------------------------------------------------------------ */
	function initAccordion() {
		$$('[data-nv-accordion]').forEach(function (accordion) {
			var multiple = '1' === accordion.getAttribute('data-nv-multiple');

			$$('.nv-accordion__header', accordion).forEach(function (header) {
				on(header, 'click', function () {
					var item = header.closest('.nv-accordion__item');
					var isOpen = item.classList.contains('is-open');

					if (!multiple) {
						$$('.nv-accordion__item.is-open', accordion).forEach(function (openItem) {
							openItem.classList.remove('is-open');
							$('.nv-accordion__header', openItem).setAttribute('aria-expanded', 'false');
						});
					}

					item.classList.toggle('is-open', !isOpen);
					header.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * فیلتر نمونه‌کارها
	 * ------------------------------------------------------------------ */
	function initFilters() {
		$$('[data-nv-filters]').forEach(function (bar) {
			var scope = bar.closest('.nv-widget') || document;
			var container = $('[data-nv-filterable]', scope);

			if (!container) {
				return;
			}

			on(bar, 'click', function (event) {
				var button = event.target.closest('[data-nv-filter]');

				if (!button) {
					return;
				}

				var filter = button.getAttribute('data-nv-filter');

				$$('[data-nv-filter]', bar).forEach(function (item) {
					item.classList.remove('is-active');
				});

				button.classList.add('is-active');

				$$('[data-nv-cat]', container).forEach(function (item) {
					var cats = (item.getAttribute('data-nv-cat') || '').split(' ');
					var show = '*' === filter || cats.indexOf(filter) > -1;

					item.hidden = !show;

					if (show) {
						item.classList.add('is-visible');
					}
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * اسلایدر
	 * ------------------------------------------------------------------ */
	function initSlider() {
		$$('[data-nv-slider]').forEach(function (slider) {
			var track = $('.nv-slider__track', slider);
			var slides = track ? $$('.nv-slide', track) : [];

			if (!slides.length) {
				return;
			}

			var options = {};

			try {
				options = JSON.parse(slider.getAttribute('data-nv-slider')) || {};
			} catch (error) {
				options = {};
			}

			var base = parseInt(
				getComputedStyle(slider).getPropertyValue('--nv-slides'),
				10
			) || 3;

			var index = 0;
			var timer = null;

			function perView() {
				var width = window.innerWidth;

				if (width <= 680) {
					return 1;
				}

				if (width <= 1024) {
					return Math.min(2, base);
				}

				return base;
			}

			function pages() {
				return Math.max(1, Math.ceil(slides.length / perView()));
			}

			function step() {
				if (!slides.length) {
					return 0;
				}

				var gap = parseFloat(getComputedStyle(track).gap) || 0;

				return slides[0].getBoundingClientRect().width + gap;
			}

			function go(newIndex) {
				var max = pages() - 1;

				index = Math.max(0, Math.min(newIndex, max));

				var offset = index * step() * (isRtl ? 1 : -1);

				track.style.transform = 'translate3d(' + offset.toFixed(2) + 'px, 0, 0)';

				$$('.nv-slider__dot', slider).forEach(function (dot, dotIndex) {
					dot.classList.toggle('is-active', dotIndex === index);
				});
			}

			function buildDots() {
				var wrap = $('[data-nv-slide-dots]', slider);

				if (!wrap) {
					return;
				}

				wrap.innerHTML = '';

				for (var i = 0; i < pages(); i++) {
					var dot = document.createElement('button');

					dot.type = 'button';
					dot.className = 'nv-slider__dot' + (0 === i ? ' is-active' : '');
					dot.setAttribute('aria-label', String(i + 1));

					/* jshint loopfunc: true */
					dot.setAttribute('data-nv-dot', String(i));
					wrap.appendChild(dot);
				}
			}

			function start() {
				if (options.autoplay && !reduceMotion) {
					stop();
					timer = window.setInterval(function () {
						go(index + 1 >= pages() ? 0 : index + 1);
					}, options.speed || 6000);
				}
			}

			function stop() {
				if (timer) {
					window.clearInterval(timer);
					timer = null;
				}
			}

			buildDots();
			go(0);
			start();

			on($('[data-nv-slide-prev]', slider), 'click', function () {
				go(index - 1);
				start();
			});

			on($('[data-nv-slide-next]', slider), 'click', function () {
				go(index + 1 >= pages() ? 0 : index + 1);
				start();
			});

			on(slider, 'click', function (event) {
				var dot = event.target.closest('[data-nv-dot]');

				if (dot) {
					go(parseInt(dot.getAttribute('data-nv-dot'), 10) || 0);
					start();
				}
			});

			on(slider, 'mouseenter', stop);
			on(slider, 'mouseleave', start);

			on(window, 'resize', function () {
				slider.style.setProperty('--nv-slides', String(perView()));
				buildDots();
				go(index);
			});

			slider.style.setProperty('--nv-slides', String(perView()));
		});
	}

	/* ------------------------------------------------------------------
	 * تغییر قیمت ماهانه / سالانه
	 * ------------------------------------------------------------------ */
	function initPricingToggle() {
		$$('[data-nv-pricing-toggle]').forEach(function (bar) {
			var scope = bar.closest('.nv-widget') || document;

			on(bar, 'click', function (event) {
				var button = event.target.closest('[data-nv-pricing]');

				if (!button) {
					return;
				}

				var mode = button.getAttribute('data-nv-pricing');

				$$('[data-nv-pricing]', bar).forEach(function (item) {
					item.classList.remove('is-active');
				});

				button.classList.add('is-active');

				$$('[data-nv-package] .nv-package__amount', scope).forEach(function (amount) {
					var value = 'yearly' === mode
						? amount.getAttribute('data-nv-price-yearly')
						: amount.getAttribute('data-nv-price-monthly');

					if (value) {
						amount.textContent = value;
					}
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * لایت‌باکس ساده برای تصاویر پروژه‌ها
	 * ------------------------------------------------------------------ */
	function initLightbox() {
		on(document, 'click', function (event) {
			var trigger = event.target.closest('a[data-nv-lightbox]');

			if (!trigger) {
				return;
			}

			event.preventDefault();

			var box = document.createElement('div');
			var close = document.createElement('button');
			var img = document.createElement('img');

			box.className = 'nv-lightbox';
			box.setAttribute('role', 'dialog');
			box.setAttribute('aria-modal', 'true');

			close.className = 'nv-lightbox__close';
			close.type = 'button';
			close.setAttribute('aria-label', 'بستن');
			close.innerHTML =
				'<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.7 5.3a1 1 0 0 0-1.4 1.4L10.6 12l-5.3 5.3a1 1 0 1 0 1.4 1.4L12 13.4l5.3 5.3a1 1 0 0 0 1.4-1.4L13.4 12l5.3-5.3a1 1 0 0 0-1.4-1.4L12 10.6 6.7 5.3Z"/></svg>';

			img.src = trigger.getAttribute('href');
			img.alt = trigger.getAttribute('aria-label') || '';

			box.appendChild(close);
			box.appendChild(img);
			document.body.appendChild(box);

			on(box, 'click', function (innerEvent) {
				if (innerEvent.target === box || innerEvent.target === close) {
					box.remove();
				}
			});
		});
	}

	/* ------------------------------------------------------------------
	 * خبرنامه (AJAX)
	 * ------------------------------------------------------------------ */
	function initNewsletter() {
		$$('form[data-nv-newsletter]').forEach(function (form) {
			on(form, 'submit', function (event) {
				event.preventDefault();

				var message = $('.nv-newsletter__msg', form);
				var input = $('input[type="email"]', form);
				var button = $('button[type="submit"]', form);

				if (!input || !input.value) {
					return;
				}

				form.classList.remove('is-error');

				if (message) {
					message.textContent = 'در حال ارسال…';
				}

				if (button) {
					button.disabled = true;
				}

				var body = new FormData();

				body.append('action', 'novin_ai_subscribe');
				body.append('nonce', settings.nonce || '');
				body.append('email', input.value);

				fetch(settings.ajaxUrl || '/wp-admin/admin-ajax.php', {
					method: 'POST',
					credentials: 'same-origin',
					body: body
				})
					.then(function (response) {
						return response.json();
					})
					.then(function (response) {
						if (message) {
							message.textContent = (response && response.data && response.data.message) || 'با موفقیت ثبت شد.';
						}

						if (response && !response.success) {
							form.classList.add('is-error');
						} else {
							input.value = '';
						}
					})
					.catch(function () {
						if (message) {
							message.textContent = 'خطا در ارسال. دوباره تلاش کنید.';
						}

						form.classList.add('is-error');
					})
					.then(function () {
						if (button) {
							button.disabled = false;
						}
					});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * دکمه بازگشت به بالا
	 * ------------------------------------------------------------------ */
	function initScrollTop() {
		var buttons = $$('.nv-scroll-top:not(.nv-scroll-top--inline)');

		if (!buttons.length) {
			return;
		}

		function update() {
			var visible = window.pageYOffset > 420;

			buttons.forEach(function (button) {
				button.classList.toggle('is-visible', visible);
			});
		}

		on(window, 'scroll', update, { passive: true });
		update();

		buttons.forEach(function (button) {
			on(button, 'click', function () {
				window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
			});
		});
	}

	/* ------------------------------------------------------------------
	 * افکت پارالاکس سبک برای لایه‌های دارای data-nv-depth
	 * ------------------------------------------------------------------ */
	function initParallax() {
		if (reduceMotion) {
			return;
		}

		var layers = $$('[data-nv-depth]');

		if (!layers.length) {
			return;
		}

		function update() {
			var offset = window.pageYOffset;

			layers.forEach(function (layer) {
				var depth = parseFloat(layer.getAttribute('data-nv-depth')) || 0.1;
				var rect = layer.getBoundingClientRect();
				var center = rect.top + rect.height / 2 - window.innerHeight / 2;

				layer.style.transform =
					'translate3d(0, ' + (-center * depth).toFixed(2) + 'px, 0)';
			});
		}

		on(window, 'scroll', function () {
			window.requestAnimationFrame(update);
		}, { passive: true });
	}

	/* ------------------------------------------------------------------
	 * اجرا
	 * ------------------------------------------------------------------ */
	/* ------------------------------------------------------------------
	 * نوارهای مهارت (رزومه تیم)
	 * ------------------------------------------------------------------ */
	function fillSkills(scope) {
		$$('[data-nv-skill]', scope).forEach(function (skill, index) {
			var bar = $('.nv-skill__bar', skill);

			if (!bar) {
				return;
			}

			var percent = parseFloat(bar.getAttribute('data-nv-percent')) || 0;

			if (reduceMotion) {
				bar.style.width = percent + '%';
				return;
			}

			bar.style.width = '0%';

			window.setTimeout(function () {
				bar.style.width = percent + '%';
			}, 120 + index * 110);
		});
	}

	function initSkills() {
		var skills = $$('[data-nv-skill]').filter(function (skill) {
			return !skill.closest('.nv-profile');
		});

		if (!skills.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			fillSkills(document);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						fillSkills(entry.target.closest('.nv-member') || entry.target);
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.3 }
		);

		skills.forEach(function (skill) {
			observer.observe(skill);
		});
	}

	/* ------------------------------------------------------------------
	 * پنجره رزومه اعضای تیم
	 * ------------------------------------------------------------------ */
	var lastFocused = null;

	function closeProfile(modal) {
		if (!modal || !modal.classList.contains('is-open')) {
			return;
		}

		modal.classList.remove('is-active');
		modal.setAttribute('aria-hidden', 'true');

		window.setTimeout(function () {
			modal.classList.remove('is-open');
			document.body.style.overflow = '';
		}, 350);

		if (lastFocused && lastFocused.focus) {
			lastFocused.focus();
			lastFocused = null;
		}
	}

	function openProfile(id) {
		var modal = document.getElementById(id);

		if (!modal) {
			return;
		}

		lastFocused = document.activeElement;

		$$('.nv-profile.is-open').forEach(closeProfile);

		modal.classList.add('is-open');
		modal.setAttribute('aria-hidden', 'false');
		document.body.style.overflow = 'hidden';

		window.requestAnimationFrame(function () {
			modal.classList.add('is-active');
			fillSkills(modal);
		});

		var close = $('.nv-profile__close', modal);

		if (close) {
			close.focus();
		}
	}

	function initProfiles() {
		if (initProfiles.bound) {
			return;
		}

		initProfiles.bound = true;

		// اتصال به صورت Delegation تا المان‌هایی که بعداً (مثلاً در ویرایشگر
		// المنتور) رندر می‌شوند هم بدون اتصال دوباره کار کنند.
		on(document, 'click', function (event) {
			var target = event.target;

			if (!target || !target.closest) {
				return;
			}

			var opener = target.closest('[data-nv-profile-open]');

			if (opener) {
				event.preventDefault();
				openProfile(opener.getAttribute('data-nv-profile-open'));
				return;
			}

			var closer = target.closest('[data-nv-profile-close]');

			if (closer) {
				event.preventDefault();
				closeProfile(closer.closest('.nv-profile'));
			}
		});

		on(document, 'keydown', function (event) {
			if ('Escape' === event.key || 'Esc' === event.key) {
				$$('.nv-profile.is-open').forEach(closeProfile);
			}
		});
	}

	function init() {
		initStickyHeader();
		initOffcanvas();
		initSearchToggle();
		initTilt();
		initMagnetic();
		initReveal();
		initCounters();
		initParticles();
		initCursorGlow();
		initAccordion();
		initFilters();
		initSlider();
		initPricingToggle();
		initLightbox();
		initNewsletter();
		initScrollTop();
		initParallax();
		initSkills();
		initProfiles();
	}

	// المنتور: پس از بارگذاری کامل المان‌ها دوباره مقداردهی اولیه انجام شود.
	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		init();

		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/global',
			function () {
				initReveal();
				initTilt();
				initSlider();
				initSkills();
				initProfiles();
			}
		);
	} else if ('loading' !== document.readyState) {
		init();
	} else {
		on(document, 'DOMContentLoaded', init);
	}

	window.NovinAiFrontend = {
		init: init,
		openProfile: openProfile,
		closeProfile: closeProfile
	};
})();
