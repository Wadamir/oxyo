const DEBUG_GESTURES = false;

document.addEventListener('DOMContentLoaded', () => {
    /* Debug gesture events */
    const debugEl = document.querySelector('.gesture-debug');

    function debugShow() {
        if (DEBUG_GESTURES && debugEl) {
            debugEl.hidden = false;
        }
    }

    function debugHide() {
        if (debugEl) debugEl.hidden = true;
    }

    function debugUpdate(data = {}) {
        if (!DEBUG_GESTURES || !debugEl) return;

        if (data.status)
            debugEl.children[1].textContent = `status: ${data.status}`;

        if (data.event)
            debugEl.children[2].textContent = `event: ${data.event}`;

        if (data.pointers !== undefined)
            debugEl.children[3].textContent = `pointers: ${data.pointers}`;

        if (data.scale !== undefined)
            debugEl.children[4].textContent = `scale: ${data.scale.toFixed(2)}`;

        if (data.last) debugEl.children[5].textContent = `last: ${data.last}`;
    }

    function debugSwiperUpdate(data = {}) {
        if (!DEBUG_GESTURES || !debugEl) return;

        const base = 7; // индекс, где начинается Swiper-блок

        if (data.event) {
            console.log(
                'debugEl.children[base + 1]',
                debugEl.children[base + 1],
            );
            if (debugEl.children[base + 1])
                debugEl.children[base + 1].textContent = `event: ${data.event}`;
        }

        if (data.touch !== undefined) {
            console.log(
                'debugEl.children[base + 2]',
                debugEl.children[base + 2],
            );
            if (debugEl.children[base + 2])
                debugEl.children[base + 2].textContent = `touch: ${data.touch}`;
        }

        if (data.slide !== undefined) {
            console.log(
                'debugEl.children[base + 3]',
                debugEl.children[base + 3],
            );
            if (debugEl.children[base + 3])
                debugEl.children[base + 3].textContent = `slide: ${data.slide}`;
        }

        if (data.allowTouchMove !== undefined) {
            console.log(
                'debugEl.children[base + 4]',
                debugEl.children[base + 4],
            );
            if (debugEl.children[base + 4])
                debugEl.children[base + 4].textContent =
                    `allowTouchMove: ${data.allowTouchMove}`;
        }
    }

    function enableDoubleTapClose(target) {
        target.addEventListener('click', () => {
            const now = Date.now();
            const diff = now - lastTap;

            if (diff < 300 && scale > 1) {
                debugUpdate({
                    event: 'double-tap',
                    last: 'close fullscreen',
                });

                resetZoom(target);
                closeFullscreen();
            } else {
                debugUpdate({
                    event: 'tap',
                    last: `delta=${diff}ms`,
                });
            }

            lastTap = now;
        });
    }

    /* End debug gesture events */

    let isFullscreen = false;
    let userPausedVideo = false;

    const closeBtn = document.querySelector('.gallery-close');

    function isMobileGalleryMode() {
        return window.matchMedia('(max-width: 991.98px)').matches;
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeFullscreen);
    }

    /* pinch to zoom support start */
    let scale = 1;
    let startScale = 1;
    let pointers = new Map();
    let isZoomed = false;

    let startDist = null;
    let lastTap = 0;

    let lastTapTime = 0;
    let didPinch = false;

    let isPanning = false;
    let panStartX = 0;
    let panStartY = 0;
    let translateX = 0;
    let translateY = 0;
    let startTranslateX = 0;
    let startTranslateY = 0;

    let videoPointerStartX = 0;
    let videoPointerStartY = 0;
    let videoPointerMoved = false;

    const MAX_EMPTY_GAP_PX = 0;
    /* pinch to zoom support end */

    const thumbsSwiper = new Swiper('.thumbs-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 12,
        freeMode: true,
        watchSlidesProgress: true,
        mousewheel: true,
        navigation: {
            prevEl: '.thumbs-prev',
            nextEl: '.thumbs-next',
        },
    });

    const mainSwiper = new Swiper('.main-swiper', {
        slidesPerView: 1,
        initialSlide: 0,
        thumbs: { swiper: thumbsSwiper },
        navigation: {
            prevEl: '.main-prev',
            nextEl: '.main-next',
        },
        on: {
            init(swiper) {
                toggleMainArrows(swiper);
                initVideoControls(swiper);
                handleVideo(swiper);
                requestAnimationFrame(() => bindZoomForActiveSlide(swiper));
                debugSwiperUpdate({
                    event: 'init',
                    allowTouchMove: swiper.allowTouchMove,
                });
            },

            touchStart(swiper, event) {
                debugSwiperUpdate({
                    event: 'touchStart',
                    touch: event?.type,
                    allowTouchMove: swiper.allowTouchMove,
                });
            },

            touchMove(swiper, event) {
                debugSwiperUpdate({
                    event: 'touchMove',
                    touch: event?.type,
                });
            },

            touchEnd(swiper) {
                debugSwiperUpdate({
                    event: 'touchEnd',
                    allowTouchMove: swiper.allowTouchMove,
                });
            },

            sliderMove(swiper) {
                debugSwiperUpdate({
                    event: 'sliderMove',
                });
            },

            slideChange(swiper) {
                resetAllZoom(swiper);

                initVideoControls(swiper);
                handleVideo(swiper);
                debugSwiperUpdate({
                    event: 'slideChange',
                    slide: swiper.activeIndex,
                });
            },

            transitionStart(swiper) {
                debugSwiperUpdate({
                    event: 'transitionStart',
                });
            },

            transitionEnd(swiper) {
                bindZoomForActiveSlide(swiper);
                debugSwiperUpdate({
                    event: 'transitionEnd',
                });
            },
        },
    });

    function bindZoomForActiveSlide(swiper = mainSwiper) {
        if (!swiper || !swiper.slides || !swiper.slides.length) return;

        const activeSlide = swiper.slides[swiper.activeIndex];
        if (!activeSlide) return;

        const active = activeSlide.querySelector('img, video');

        if (active) {
            enablePinchZoom(active);
            enableDoubleTapClose(active);
        }
    }

    /* pinch to zoom support start */
    mainSwiper.on('slideChange', () => {
        // Keep for non-animated/instant slide switches.
        bindZoomForActiveSlide(mainSwiper);
    });
    /* pinch to zoom support end */

    function isZoomableElement(el) {
        return el && el.tagName === 'IMG';
    }

    function toggleMainArrows(swiper) {
        const root = swiper.el;
        if (!root) return;

        if (swiper.slides.length <= 1) {
            root.classList.add('no-arrows');
        } else {
            root.classList.remove('no-arrows');
        }
    }

    function initVideoControls(swiper) {
        const slide = swiper.slides[swiper.activeIndex];
        if (!slide || !slide.classList.contains('is-video')) return;

        const video = slide.querySelector('video');
        const wrapper = slide.querySelector('.video-wrapper');
        if (!video || !wrapper) return;

        if (video.__controlsBound) return;
        video.__controlsBound = true;

        video.addEventListener('pointerdown', (e) => {
            videoPointerStartX = e.clientX;
            videoPointerStartY = e.clientY;
            videoPointerMoved = false;
        });

        video.addEventListener('pointermove', (e) => {
            const dx = Math.abs(e.clientX - videoPointerStartX);
            const dy = Math.abs(e.clientY - videoPointerStartY);

            if (dx > 6 || dy > 6) {
                videoPointerMoved = true;
            }
        });

        video.addEventListener('pointerup', (e) => {
            if (videoPointerMoved) return;

            if (video.paused) {
                video.play().catch(() => {});
                wrapper.classList.remove('is-paused');
                userPausedVideo = false;
            } else {
                video.pause();
                wrapper.classList.add('is-paused');
                userPausedVideo = true;
            }
        });
    }

    function handleVideo(swiper) {
        const slide = swiper.slides[swiper.activeIndex];
        if (!slide || !slide.classList.contains('is-video')) return;

        const video = slide.querySelector('video');
        const wrapper = slide.querySelector('.video-wrapper');
        if (!video || !wrapper) return;

        // If video is already marked as loaded, do nothing
        if (wrapper.classList.contains('is-loaded')) return;

        console.log('handle video');

        const markLoaded = () => {
            console.log('video loaded');
            wrapper.classList.add('is-loaded');
            wrapper.classList.remove('is-paused');
        };

        // If video frame is already available, mark as loaded immediately
        if (video.readyState >= 2) {
            markLoaded();
        } else {
            video.addEventListener('loadeddata', markLoaded, { once: true });
        }

        if (!userPausedVideo) {
            video.play().catch(() => {});
        }
    }

    function openFullscreen() {
        if (isMobileGalleryMode()) return;
        if (isFullscreen) return;
        isFullscreen = true;

        document.body.classList.add('no-scroll');
        document
            .querySelector('.product-gallery-wrapper')
            .classList.add('is-fullscreen');

        /* debug gesture events */
        debugShow();
        debugUpdate({ status: 'fullscreen opened' });
        /* end debug gesture events */

        // iOs fix for Swiper not updating correctly
        requestAnimationFrame(() => {
            mainSwiper.update();

            /* pinch to zoom support start */
            requestAnimationFrame(() => {
                bindZoomForActiveSlide(mainSwiper);
            });
            /* pinch to zoom support end */

            mainSwiper.updateSlides();
            mainSwiper.updateSize();
        });
    }

    function closeFullscreen() {
        if (!isFullscreen) return;
        isFullscreen = false;

        document.body.classList.remove('no-scroll');
        document
            .querySelector('.product-gallery-wrapper')
            .classList.remove('is-fullscreen');

        /* debug gesture events */
        debugUpdate({ status: 'fullscreen closed' });
        debugHide();
        /* end debug gesture events */

        mainSwiper.update();
    }

    /* pinch to zoom support start */
    function enablePinchZoom(target) {
        if (!isZoomableElement(target)) return;
        if (target.__pinchZoomEnabled) return;

        target.__pinchZoomEnabled = true;
        target.style.touchAction = 'pan-y';
        target.addEventListener('pointerdown', onPointerDown);
        target.addEventListener('pointermove', onPointerMove);
        target.addEventListener('pointerup', onPointerUp);
        target.addEventListener('pointercancel', onPointerUp);

        // iOS Safari/Chrome fallback: ensure pinch works via touch events as well.
        target.addEventListener('touchstart', onTouchStart, { passive: false });
        target.addEventListener('touchmove', onTouchMove, { passive: false });
        target.addEventListener('touchend', onTouchEnd, { passive: false });
        target.addEventListener('touchcancel', onTouchEnd, { passive: false });
    }

    function getTouchDistance(t1, t2) {
        const dx = t1.clientX - t2.clientX;
        const dy = t1.clientY - t2.clientY;
        return Math.hypot(dx, dy);
    }

    function onTouchStart(e) {
        if (!isMobileGalleryMode()) return;

        const target = e.currentTarget;
        const touches = e.touches;
        if (!touches || !touches.length) return;

        if (touches.length === 2) {
            e.preventDefault();
            startDist = getTouchDistance(touches[0], touches[1]);
            startScale = scale;
            isPanning = false;
            didPinch = true;
            mainSwiper.allowTouchMove = false;
            return;
        }

        if (touches.length === 1 && scale > 1) {
            const t = touches[0];
            isPanning = true;
            panStartX = t.clientX;
            panStartY = t.clientY;
            startTranslateX = translateX;
            startTranslateY = translateY;

            // Avoid Swiper stealing one-finger pan when image is zoomed.
            e.preventDefault();
            mainSwiper.allowTouchMove = false;
            applyTransform(target);
        }
    }

    function onTouchMove(e) {
        if (!isMobileGalleryMode()) return;

        const target = e.currentTarget;
        const touches = e.touches;
        if (!touches || !touches.length) return;

        if (touches.length === 2) {
            const dist = getTouchDistance(touches[0], touches[1]);

            if (!startDist) {
                startDist = dist;
                startScale = scale;
            }

            // Точка в середине двух пальцев (в координатах окна)
            const pinchCenterX = (touches[0].clientX + touches[1].clientX) / 2;
            const pinchCenterY = (touches[0].clientY + touches[1].clientY) / 2;

            const oldScale = scale;
            const nextScale = startScale * (dist / startDist);
            scale = Math.min(Math.max(nextScale, 1), 4);

            // Масштабировать от точки между пальцами
            if (oldScale !== scale && target) {
                const container = target.closest('.media-wrapper');
                if (container) {
                    const rect = container.getBoundingClientRect();
                    // Координаты точки пинча относительно контейнера
                    const pinchX = pinchCenterX - rect.left;
                    const pinchY = pinchCenterY - rect.top;

                    // Точка на изображении в координатах изображения (до масштабирования)
                    const imgPointX = (pinchX - translateX) / oldScale;
                    const imgPointY = (pinchY - translateY) / oldScale;

                    // Новое смещение для зума к точке пальцев
                    translateX = pinchX - imgPointX * scale;
                    translateY = pinchY - imgPointY * scale;
                }
            }

            didPinch = true;
            isPanning = false;
            mainSwiper.allowTouchMove = false;

            e.preventDefault();
            applyTransform(target);
            return;
        }

        if (touches.length === 1 && isPanning && scale > 1) {
            const t = touches[0];
            translateX = startTranslateX + (t.clientX - panStartX);
            translateY = startTranslateY + (t.clientY - panStartY);
            mainSwiper.allowTouchMove = false;

            e.preventDefault();
            applyTransform(target);
        }
    }

    function onTouchEnd(e) {
        if (!isMobileGalleryMode()) return;

        const target = e.currentTarget;

        const touches = e.touches;

        if (!touches || touches.length < 2) {
            startDist = null;
            startScale = scale;
        }

        if (!touches || touches.length === 0) {
            isPanning = false;
            normalizeToCenterIfBaseScale();
            if (target) {
                applyTransformSmooth(target);
            }
        }
    }

    function onPointerDown(e) {
        if (!isZoomableElement(e.target)) return;

        pointers.set(e.pointerId, e);

        // As soon as second finger appears, block Swiper drag and lock pinch baseline.
        if (pointers.size === 2) {
            const [p1, p2] = Array.from(pointers.values());
            startDist = Math.hypot(
                p1.clientX - p2.clientX,
                p1.clientY - p2.clientY,
            );
            startScale = scale;
            mainSwiper.allowTouchMove = false;
        }

        if (scale > 1 && pointers.size === 1) {
            isPanning = true;
            panStartX = e.clientX;
            panStartY = e.clientY;
            startTranslateX = translateX;
            startTranslateY = translateY;
        }

        const now = Date.now();

        if (
            pointers.size === 1 &&
            now - lastTapTime < 300 &&
            scale > 1 &&
            !didPinch
        ) {
            debugUpdate({
                event: 'double-tap',
                last: 'reset zoom',
            });

            resetZoom(e.target);
            lastTapTime = 0;
            return;
        }

        lastTapTime = now;
        didPinch = false;

        debugUpdate({
            event: 'pointerdown',
            pointers: pointers.size,
        });
    }

    function onPointerMove(e) {
        if (!pointers.has(e.pointerId)) return;

        pointers.set(e.pointerId, e);

        // Pinch
        if (pointers.size === 2) {
            didPinch = true;
            isPanning = false;
            mainSwiper.allowTouchMove = false;

            const [p1, p2] = Array.from(pointers.values());
            const dist = getDistance(p1, p2);

            if (!startDist) {
                startDist = dist;
            }

            // Точка в середине двух пальцев (в координатах окна)
            const pinchCenterX = (p1.clientX + p2.clientX) / 2;
            const pinchCenterY = (p1.clientY + p2.clientY) / 2;

            const oldScale = scale;
            const nextScale = startScale * (dist / startDist);
            scale = Math.min(Math.max(nextScale, 1), 4);

            // Масштабировать от точки между пальцами
            if (oldScale !== scale && e.target) {
                const container = e.target.closest('.media-wrapper');
                if (container) {
                    const rect = container.getBoundingClientRect();
                    // Координаты точки пинча относительно контейнера
                    const pinchX = pinchCenterX - rect.left;
                    const pinchY = pinchCenterY - rect.top;

                    // Точка на изображении в координатах изображения (до масштабирования)
                    const imgPointX = (pinchX - translateX) / oldScale;
                    const imgPointY = (pinchY - translateY) / oldScale;

                    // Новое смещение для зума к точке пальцев
                    translateX = pinchX - imgPointX * scale;
                    translateY = pinchY - imgPointY * scale;
                }
            }

            applyTransform(e.target);
            return;
        }

        // Pan
        if (isPanning && scale > 1) {
            const dx = e.clientX - panStartX;
            const dy = e.clientY - panStartY;

            translateX = startTranslateX + dx;
            translateY = startTranslateY + dy;

            applyTransform(e.target);
        }

        debugUpdate({
            event: 'pointermove',
            pointers: pointers.size,
            scale,
        });
    }

    function onPointerUp(e) {
        pointers.delete(e.pointerId);

        if (pointers.size < 2) {
            startDist = null;
            startScale = scale;
        }

        if (pointers.size === 0) {
            isPanning = false;
            normalizeToCenterIfBaseScale();
            applyTransformSmooth(e.currentTarget || e.target);
        }

        debugUpdate({
            event: 'pointerup',
            pointers: pointers.size,
        });
    }

    function getDistance(p1, p2) {
        const dx = p1.clientX - p2.clientX;
        const dy = p1.clientY - p2.clientY;
        const dist = Math.hypot(dx, dy);
        if (!startDist) startDist = dist;
        return dist;
    }

    function normalizeToCenterIfBaseScale() {
        // Snap near-1 scale back to exact base state so image recenters after pinch-out.
        if (scale <= 1.02) {
            scale = 1;
            translateX = 0;
            translateY = 0;
            startScale = 1;
        }
    }

    function clampTranslateToContainer(el) {
        if (!el || scale <= 1) return;

        const container = el.closest('.media-wrapper');
        if (!container) return;

        const baseWidth = el.offsetWidth;
        const baseHeight = el.offsetHeight;
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;

        if (!baseWidth || !baseHeight || !containerWidth || !containerHeight)
            return;

        const scaledWidth = baseWidth * scale;
        const scaledHeight = baseHeight * scale;

        const maxShiftX = Math.max(
            0,
            (scaledWidth - containerWidth) / 2 + MAX_EMPTY_GAP_PX,
        );
        const maxShiftY = Math.max(
            0,
            (scaledHeight - containerHeight) / 2 + MAX_EMPTY_GAP_PX,
        );

        translateX = Math.min(maxShiftX, Math.max(-maxShiftX, translateX));
        translateY = Math.min(maxShiftY, Math.max(-maxShiftY, translateY));
    }

    function applyTransform(el) {
        normalizeToCenterIfBaseScale();
        clampTranslateToContainer(el);
        el.style.transition = '';
        el.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
        el.style.touchAction = scale > 1 ? 'none' : 'pan-y';

        mainSwiper.allowTouchMove = scale === 1;
    }

    function applyTransformSmooth(el) {
        normalizeToCenterIfBaseScale();
        clampTranslateToContainer(el);

        // Use requestAnimationFrame to ensure smooth transition
        el.style.transition = '';
        el.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;

        // Force reflow and then apply transition
        void el.offsetWidth;

        requestAnimationFrame(() => {
            el.style.transition = 'transform 300ms ease-out';
            el.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
            el.style.touchAction = scale > 1 ? 'none' : 'pan-y';
            mainSwiper.allowTouchMove = scale === 1;

            el.addEventListener(
                'transitionend',
                function removeTransition() {
                    el.style.transition = '';
                    el.removeEventListener('transitionend', removeTransition);
                },
                { once: true },
            );
        });
    }

    function resetZoom(target) {
        scale = 1;
        startScale = 1;
        startDist = null;
        pointers.clear();

        translateX = 0;
        translateY = 0;

        isPanning = false;
        didPinch = false;

        target.style.transform = 'translate(0, 0) scale(1)';
        target.style.touchAction = 'pan-y';
        mainSwiper.allowTouchMove = true;
    }

    function resetAllZoom(swiper) {
        scale = 1;
        startScale = 1;
        startDist = null;
        pointers.clear();
        isPanning = false;
        didPinch = false;

        translateX = 0;
        translateY = 0;

        mainSwiper.allowTouchMove = true;

        swiper.slides.forEach((slide) => {
            const img = slide.querySelector('img');
            if (img) {
                img.style.transform = 'translate(0, 0) scale(1)';
                img.style.touchAction = 'pan-y';
            }
        });
    }

    document
        .querySelector('.video-fullscreen-btn')
        ?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (isMobileGalleryMode()) return;
            openFullscreen();
        });

    document.querySelectorAll('.image-wrapper a').forEach((link) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            if (isMobileGalleryMode()) return;
            openFullscreen();
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeFullscreen();
        }
    });
});
