/**
 * ReactBits DepthCarousel Engine Implementation (GSAP 3D Depth Mechanics)
 */
document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('.depth-carousel');
  if (!root || typeof gsap === 'undefined') return;

  const cardRefs = Array.from(root.querySelectorAll('.depth-carousel__card'));
  const overlayRefs = Array.from(root.querySelectorAll('.depth-carousel__tint'));
  const dotRefs = Array.from(root.querySelectorAll('.depth-carousel__dot'));
  const prevBtn = root.querySelector('.depth-carousel__arrow--prev');
  const nextBtn = root.querySelector('.depth-carousel__arrow--next');

  const count = cardRefs.length;
  if (count === 0) return;

  const config = {
    depth: 220,
    spread: 105,
    tilt: 12,
    tiltDirection: 'right',
    visibleCards: 4,
    falloff: 0.2,
    blur: 8,
    duration: 200,
    ease: 'power3.out',
    loop: true,
    cardWidth: 390,
    cardHeight: 480,
    radius: 36,
    tint: '#7b5900',
    autoplayDelay: 800
  };

  let pos = 0;
  let focusIdx = 0;
  let tween = null;
  let scale = 1;
  let autoTimer = null;
  let isDragging = false;
  let dragStartPos = 0;
  let dragStartX = 0;

  const clamp = (v, min, max) => Math.min(Math.max(v, min), max);

  const layout = (currentPos) => {
    const dir = config.tiltDirection === 'left' ? -1 : 1;
    const sc = scale;

    for (let i = 0; i < count; i++) {
      const el = cardRefs[i];
      if (!el) continue;

      let d = i - currentPos;
      if (config.loop && count > 1) {
        d = ((d % count) + count) % count;
        if (d > count / 2) d -= count;
      }

      const back = Math.max(0, d);
      const az = Math.abs(d);
      const shown = az <= config.visibleCards + 0.5;

      const tz = -config.depth * d;
      const tx = dir * config.spread * d;
      const ry = dir * config.tilt * clamp(d, 0, 1);

      let opacity = d < 0 ? Math.max(0, 1 + d) : 1;
      if (!shown) opacity = 0;

      const brightness = Math.max(0.15, 1 - back * config.falloff);
      const blurPx = config.blur > 0 ? Math.min(config.blur, (back / Math.max(1, config.visibleCards)) * config.blur) : 0;
      const zi = Math.round(2000 - d * 20);

      el.style.transform = `translate(-50%, -50%) scale(${sc}) translateX(${tx.toFixed(2)}px) translateZ(${tz.toFixed(2)}px) rotateY(${ry.toFixed(3)}deg)`;
      el.style.opacity = opacity.toFixed(3);
      el.style.filter = `brightness(${brightness.toFixed(3)}) blur(${blurPx.toFixed(2)}px)`;
      el.style.zIndex = String(zi);
      el.style.pointerEvents = shown && opacity > 0.05 ? 'auto' : 'none';

      const ov = overlayRefs[i];
      if (ov) ov.style.opacity = clamp(back * config.falloff * 1.25, 0, 0.86).toFixed(3);
    }
  };

  const updateDots = (idx) => {
    dotRefs.forEach((dot, i) => {
      if (i === idx) dot.classList.add('is-active');
      else dot.classList.remove('is-active');
    });
  };

  const tweenTo = (target) => {
    if (tween) tween.kill();
    const proxy = { p: pos };
    tween = gsap.to(proxy, {
      p: target,
      duration: config.duration / 1000,
      ease: config.ease,
      onUpdate: () => {
        pos = proxy.p;
        layout(pos);
      },
      onComplete: () => {
        pos = ((pos % count) + count) % count;
        layout(pos);
      }
    });
  };

  const setFocus = (rawIndex) => {
    const idx = config.loop ? ((rawIndex % count) + count) % count : clamp(rawIndex, 0, count - 1);
    let delta = idx - pos;
    if (config.loop && count > 1) {
      delta = ((delta % count) + count) % count;
      if (delta > count / 2) delta -= count;
    }
    tweenTo(pos + delta);
    focusIdx = idx;
    updateDots(idx);
  };

  const navigateBy = (step) => setFocus(focusIdx + step);

  // Resize Scale Observer
  const updateScale = () => {
    const needed = config.cardWidth + Math.abs(config.spread) * 2 + 120;
    scale = clamp(root.clientWidth / needed, 0.5, 1);
    layout(pos);
  };
  window.addEventListener('resize', updateScale);
  updateScale();

  // Control Buttons
  if (prevBtn) prevBtn.addEventListener('click', (e) => { e.stopPropagation(); navigateBy(-1); });
  if (nextBtn) nextBtn.addEventListener('click', (e) => { e.stopPropagation(); navigateBy(1); });

  // Indicators Click
  dotRefs.forEach((dot, i) => {
    dot.addEventListener('click', (e) => {
      e.stopPropagation();
      setFocus(i);
    });
  });

  // Card Clicks
  cardRefs.forEach((card, i) => {
    card.addEventListener('click', () => setFocus(i));
  });

  // Drag Interaction
  const onPointerDown = (e) => {
    isDragging = true;
    dragStartX = e.clientX;
    dragStartPos = pos;
    if (tween) tween.kill();
  };

  const onPointerMove = (e) => {
    if (!isDragging) return;
    const dx = e.clientX - dragStartX;
    const stepPx = Math.max(config.cardWidth * 0.55 * scale, 40);
    pos = dragStartPos - dx / stepPx;
    layout(pos);
  };

  const onPointerUp = () => {
    if (!isDragging) return;
    isDragging = false;
    setFocus(Math.round(pos));
  };

  root.addEventListener('mousedown', onPointerDown);
  window.addEventListener('mousemove', onPointerMove);
  window.addEventListener('mouseup', onPointerUp);

  root.addEventListener('touchstart', (e) => onPointerDown(e.touches[0]));
  window.addEventListener('touchmove', (e) => onPointerMove(e.touches[0]));
  window.addEventListener('touchend', onPointerUp);

  // Autoplay (Continuous Auto Scroll to Right Side One by One)
  const startAutoplay = () => {
    if (autoTimer) clearInterval(autoTimer);
    autoTimer = setInterval(() => {
      navigateBy(1); // Auto scroll right side one by one
    }, config.autoplayDelay);
  };

  const stopAutoplay = () => {
    if (autoTimer) {
      clearInterval(autoTimer);
      autoTimer = null;
    }
  };

  root.addEventListener('mouseenter', stopAutoplay);
  root.addEventListener('mouseleave', startAutoplay);
  root.addEventListener('touchstart', stopAutoplay, { passive: true });
  root.addEventListener('touchend', () => {
    setTimeout(startAutoplay, 1500);
  });

  // Start continuous auto-scroll immediately
  startAutoplay();

  // Initial render
  layout(pos);
});
