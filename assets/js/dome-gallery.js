// Official ReactBits DomeGallery Vanilla Engine
// Implements 3D Sphere Dome Gallery with inertia drag, grid item coordinates, & tile enlargement

function buildItems(pool, seg) {
  const xCols = Array.from({ length: seg }, (_, i) => -37 + i * 2);
  const evenYs = [-4, -2, 0, 2, 4];
  const oddYs = [-3, -1, 1, 3, 5];

  const coords = xCols.flatMap((x, c) => {
    const ys = c % 2 === 0 ? evenYs : oddYs;
    return ys.map(y => ({ x, y, sizeX: 2, sizeY: 2 }));
  });

  const totalSlots = coords.length;
  if (pool.length === 0) return coords.map(c => ({ ...c, src: '', alt: '' }));

  const normalizedImages = pool.map(image => typeof image === 'string' ? { src: image, alt: '' } : { src: image.src || '', alt: image.alt || '' });
  const usedImages = Array.from({ length: totalSlots }, (_, i) => normalizedImages[i % normalizedImages.length]);

  return coords.map((c, i) => ({
    ...c,
    src: usedImages[i].src,
    alt: usedImages[i].alt
  }));
}

class DomeGalleryEngine {
  constructor(containerId, options = {}) {
    this.container = typeof containerId === 'string' ? document.getElementById(containerId) : containerId;
    if (!this.container) return;

    this.images = options.images || [];
    this.segments = options.segments || 34;
    this.fit = options.fit || 0.65;
    this.minRadius = options.minRadius || 700;
    this.maxVerticalRotationDeg = options.maxVerticalRotationDeg !== undefined ? options.maxVerticalRotationDeg : 5;
    this.dragDampening = options.dragDampening || 2;
    this.grayscale = options.grayscale || false;

    this.rotation = { x: 0, y: 0 };
    this.startRot = { x: 0, y: 0 };
    this.startPos = { x: 0, y: 0 };
    this.isDragging = false;
    this.moved = false;
    this.velocity = { x: 0, y: 0 };

    this.items = buildItems(this.images, this.segments);
    this.buildHTML();
    this.initEvents();
  }

  buildHTML() {
    this.container.innerHTML = '';
    this.container.className = 'sphere-root';
    this.container.style.setProperty('--segments-x', this.segments);
    this.container.style.setProperty('--segments-y', this.segments);
    this.container.style.setProperty('--radius', `${this.minRadius}px`);

    this.main = document.createElement('main');
    this.main.className = 'sphere-main';

    this.stage = document.createElement('div');
    this.stage.className = 'stage';

    this.sphere = document.createElement('div');
    this.sphere.className = 'sphere';

    // Render Sphere Items
    this.items.forEach((it, i) => {
      const itemEl = document.createElement('div');
      itemEl.className = 'item';
      itemEl.setAttribute('data-src', it.src);
      itemEl.setAttribute('data-offset-x', it.x);
      itemEl.setAttribute('data-offset-y', it.y);
      itemEl.setAttribute('data-size-x', it.sizeX);
      itemEl.setAttribute('data-size-y', it.sizeY);

      itemEl.style.setProperty('--offset-x', it.x);
      itemEl.style.setProperty('--offset-y', it.y);
      itemEl.style.setProperty('--item-size-x', it.sizeX);
      itemEl.style.setProperty('--item-size-y', it.sizeY);

      const imgWrapper = document.createElement('div');
      imgWrapper.className = 'item__image';
      imgWrapper.setAttribute('role', 'button');
      imgWrapper.setAttribute('tabindex', '0');

      const img = document.createElement('img');
      img.src = it.src;
      img.alt = it.alt || 'Placement Poster';
      img.draggable = false;

      imgWrapper.appendChild(img);
      itemEl.appendChild(imgWrapper);
      this.sphere.appendChild(itemEl);

      // Click to open enlarge modal
      imgWrapper.addEventListener('click', (e) => {
        if (!this.moved) this.openEnlarge(it.src);
      });
    });

    this.stage.appendChild(this.sphere);
    this.main.appendChild(this.stage);

    // Overlays & Vignettes
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    this.main.appendChild(overlay);

    const overlayBlur = document.createElement('div');
    overlayBlur.className = 'overlay overlay--blur';
    this.main.appendChild(overlayBlur);

    const fadeTop = document.createElement('div');
    fadeTop.className = 'edge-fade edge-fade--top';
    this.main.appendChild(fadeTop);

    const fadeBottom = document.createElement('div');
    fadeBottom.className = 'edge-fade edge-fade--bottom';
    this.main.appendChild(fadeBottom);

    // Viewer Modal
    this.viewer = document.createElement('div');
    this.viewer.className = 'viewer';
    
    this.scrim = document.createElement('div');
    this.scrim.className = 'scrim';
    this.viewer.appendChild(this.scrim);

    this.frame = document.createElement('div');
    this.frame.className = 'frame';
    this.viewer.appendChild(this.frame);

    this.main.appendChild(this.viewer);
    this.container.appendChild(this.main);

    // Close scrim listener
    this.scrim.addEventListener('click', () => this.closeEnlarge());
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') this.closeEnlarge();
    });

    this.applyTransform(0, 0);
  }

  applyTransform(xDeg, yDeg) {
    if (this.sphere) {
      this.sphere.style.transform = `translateZ(calc(var(--radius) * -1)) rotateX(${xDeg}deg) rotateY(${yDeg}deg)`;
    }
  }

  initEvents() {
    const onDown = (e) => {
      if (this.container.getAttribute('data-enlarging') === 'true') return;
      this.isDragging = true;
      this.moved = false;
      const evt = e.touches ? e.touches[0] : e;
      this.startPos = { x: evt.clientX, y: evt.clientY };
      this.startRot = { ...this.rotation };
      this.container.style.cursor = 'grabbing';
    };

    const onMove = (e) => {
      if (!this.isDragging) return;
      const evt = e.touches ? e.touches[0] : e;
      const dx = evt.clientX - this.startPos.x;
      const dy = evt.clientY - this.startPos.y;

      if (Math.abs(dx) > 4 || Math.abs(dy) > 4) this.moved = true;

      const maxV = this.maxVerticalRotationDeg;
      const nextX = Math.min(maxV, Math.max(-maxV, this.startRot.x - dy / 18));
      const nextY = this.startRot.y + dx / 18;

      this.rotation = { x: nextX, y: nextY };
      this.velocity = { x: dx * 0.04, y: dy * 0.04 };
      this.applyTransform(nextX, nextY);
    };

    const onUp = () => {
      if (!this.isDragging) return;
      this.isDragging = false;
      this.container.style.cursor = 'grab';
      this.startInertia();
    };

    this.main.addEventListener('mousedown', onDown);
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onUp);

    this.main.addEventListener('touchstart', onDown, { passive: true });
    window.addEventListener('touchmove', onMove, { passive: true });
    window.addEventListener('touchend', onUp);
  }

  startInertia() {
    if (Math.abs(this.velocity.x) < 0.01 && Math.abs(this.velocity.y) < 0.01) return;
    
    this.rotation.y += this.velocity.x;
    const maxV = this.maxVerticalRotationDeg;
    this.rotation.x = Math.min(maxV, Math.max(-maxV, this.rotation.x - this.velocity.y * 0.3));

    this.velocity.x *= 0.94;
    this.velocity.y *= 0.94;

    this.applyTransform(this.rotation.x, this.rotation.y);
    requestAnimationFrame(() => this.startInertia());
  }

  openEnlarge(src) {
    this.container.setAttribute('data-enlarging', 'true');
    const existing = this.viewer.querySelector('.enlarge');
    if (existing) existing.remove();

    const enlarge = document.createElement('div');
    enlarge.className = 'enlarge';
    enlarge.style.width = '380px';
    enlarge.style.height = '480px';

    const img = document.createElement('img');
    img.src = src;
    enlarge.appendChild(img);

    this.viewer.appendChild(enlarge);
  }

  closeEnlarge() {
    this.container.removeAttribute('data-enlarging');
    const enlarge = this.viewer.querySelector('.enlarge');
    if (enlarge) enlarge.remove();
  }
}

let dome2021Instance = null;
let dome2022Instance = null;

function initDome2021() {
  const domeContainer = document.getElementById('placementDomeGallery');
  if (domeContainer && (!dome2021Instance || domeContainer.children.length === 0)) {
    dome2021Instance = new DomeGalleryEngine('placementDomeGallery', {
      images: [
        { src: 'assets/placements/28.png', alt: 'Placement Poster 28' },
        { src: 'assets/placements/29.png', alt: 'Placement Poster 29' },
        { src: 'assets/placements/30.png', alt: 'Placement Poster 30' },
        { src: 'assets/placements/31.png', alt: 'Placement Poster 31' },
        { src: 'assets/placements/32.png', alt: 'Placement Poster 32' },
        { src: 'assets/placements/33.png', alt: 'Placement Poster 33' },
        { src: 'assets/placements/34.png', alt: 'Placement Poster 34' },
        { src: 'assets/placements/35.png', alt: 'Placement Poster 35' },
        { src: 'assets/placements/36.png', alt: 'Placement Poster 36' },
        { src: 'assets/placements/37.png', alt: 'Placement Poster 37' },
        { src: 'assets/placements/38.png', alt: 'Placement Poster 38' },
        { src: 'assets/placements/39.png', alt: 'Placement Poster 39' },
        { src: 'assets/placements/40.png', alt: 'Placement Poster 40' }
      ],
      fit: 0.8,
      minRadius: 650,
      maxVerticalRotationDeg: 0,
      segments: 34,
      dragDampening: 2,
      grayscale: false
    });
  }
}

function initDome2022() {
  const dome2022_26Container = document.getElementById('placement2022_26DomeGallery');
  if (dome2022_26Container && (!dome2022Instance || dome2022_26Container.children.length === 0)) {
    const p2022_26_images = [];
    for (let i = 1; i <= 53; i++) {
      p2022_26_images.push({
        src: `assets/images/placements_2022_26/poster_${i}.png`,
        alt: `Placement 2022-26 Poster ${i}`
      });
    }

    dome2022Instance = new DomeGalleryEngine('placement2022_26DomeGallery', {
      images: p2022_26_images,
      fit: 0.8,
      minRadius: 650,
      maxVerticalRotationDeg: 0,
      segments: 34,
      dragDampening: 2,
      grayscale: false
    });
  }
}

window.initDome2021 = initDome2021;
window.initDome2022 = initDome2022;

document.addEventListener('DOMContentLoaded', () => {
  initDome2021();
  initDome2022();
});
