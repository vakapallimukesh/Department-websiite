/**
 * ReactBits OrbitImages Component Engine & Horizontal Scroll Controller
 */
document.addEventListener('DOMContentLoaded', () => {
  // --- 1. Orbit Engine Setup ---
  const container = document.querySelector('.orbit-container');
  if (container) {
    const items = Array.from(container.querySelectorAll('.orbit-item'));
    const svgPath = container.querySelector('.orbit-path-svg path');
    const count = items.length;

    if (count > 0) {
      const config = {
        radiusX: 340,
        radiusY: 110,
        rotationDeg: -8,
        duration: 28, // seconds for full loop
        paused: false
      };

      let progress = 0;
      let lastTime = performance.now();

      const getContainerDimensions = () => {
        return {
          w: container.clientWidth || 800,
          h: container.clientHeight || 380
        };
      };

      const generateSvgPath = (cx, cy, rx, ry, rotRad) => {
        if (!svgPath) return;
        const points = [];
        const steps = 60;
        for (let i = 0; i <= steps; i++) {
          const angle = (i / steps) * 2 * Math.PI;
          const unrotX = rx * Math.cos(angle);
          const unrotY = ry * Math.sin(angle);
          const px = cx + unrotX * Math.cos(rotRad) - unrotY * Math.sin(rotRad);
          const py = cy + unrotX * Math.sin(rotRad) + unrotY * Math.cos(rotRad);
          points.push(i === 0 ? `M ${px.toFixed(1)} ${py.toFixed(1)}` : `L ${px.toFixed(1)} ${py.toFixed(1)}`);
        }
        svgPath.setAttribute('d', points.join(' ') + ' Z');
      };

      const render = (now) => {
        const dt = Math.min((now - lastTime) / 1000, 0.1); // Cap delta to prevent jump after background tab
        lastTime = now;

        if (!config.paused) {
          progress += (dt / config.duration) * 2 * Math.PI;
          if (progress > 2 * Math.PI) progress -= 2 * Math.PI;
        }

        const { w, h } = getContainerDimensions();
        const cx = w / 2;
        const cy = h / 2;

        const scaleRatio = Math.min(w / 850, 1);
        const rx = config.radiusX * scaleRatio;
        const ry = config.radiusY * scaleRatio;
        const rotRad = (config.rotationDeg * Math.PI) / 180;

        generateSvgPath(cx, cy, rx, ry, rotRad);

        items.forEach((item, index) => {
          const itemAngle = (index / count) * 2 * Math.PI + progress;
          const unrotX = rx * Math.cos(itemAngle);
          const unrotY = ry * Math.sin(itemAngle);

          const px = cx + unrotX * Math.cos(rotRad) - unrotY * Math.sin(rotRad);
          const py = cy + unrotX * Math.sin(rotRad) + unrotY * Math.cos(rotRad);

          const depthFactor = (unrotY + ry) / (2 * ry);
          const zi = Math.round(10 + depthFactor * 50);
          const scale = 0.92 + depthFactor * 0.22;
          const opacity = 0.80 + depthFactor * 0.20;

          const itemHalf = (item.offsetWidth / 2) || 55;

          // Hardware-accelerated GPU transform
          item.style.transform = `translate3d(${(px - itemHalf).toFixed(1)}px, ${(py - itemHalf).toFixed(1)}px, 0) scale(${scale.toFixed(2)})`;
          item.style.zIndex = String(zi);
          item.style.opacity = opacity.toFixed(2);
        });

        requestAnimationFrame(render);
      };

      // Hover Pause Interaction
      container.addEventListener('mouseenter', () => { config.paused = true; });
      container.addEventListener('mouseleave', () => { config.paused = false; lastTime = performance.now(); });

      requestAnimationFrame(render);
    }
  }

  // --- 2. Horizontal Scroll Controllers ---
  const scrollRow = document.querySelector('.startups-scroll-row');
  const prevBtn = document.querySelector('.startups-scroll-prev');
  const nextBtn = document.querySelector('.startups-scroll-next');

  if (scrollRow) {
    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        scrollRow.scrollBy({ left: -380, behavior: 'smooth' });
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        scrollRow.scrollBy({ left: 380, behavior: 'smooth' });
      });
    }
  }
});
