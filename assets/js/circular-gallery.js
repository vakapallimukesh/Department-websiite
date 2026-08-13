// Circular Gallery WebGL / Canvas Renderer for SRKREC Department Website
// Ported from ReactBits CircularGallery component
class CircularGalleryEngine {
  constructor(containerId, options = {}) {
    this.container = typeof containerId === 'string' ? document.getElementById(containerId) : containerId;
    if (!this.container) return;

    this.items = options.items || [];
    this.bend = options.bend !== undefined ? options.bend : 1;
    this.textColor = options.textColor || '#ffffff';
    this.scrollSpeed = options.scrollSpeed || 2;
    this.scrollEase = options.scrollEase || 0.08;

    this.canvas = document.createElement('canvas');
    this.canvas.style.cssText = 'width: 100%; height: 100%; display: block; cursor: grab;';
    this.container.appendChild(this.canvas);
    this.ctx = this.canvas.getContext('2d');

    this.scroll = { current: 0, target: 0, ease: this.scrollEase };
    this.isDragging = false;
    this.startX = 0;
    this.startScroll = 0;

    this.images = [];
    this.loadImages();
    this.initEvents();
    this.resize();
    this.animate();
  }

  loadImages() {
    this.items.forEach((item, i) => {
      const img = new Image();
      img.crossOrigin = 'anonymous';
      img.src = item.image;
      this.images.push({ img, text: item.text, loaded: false });
      img.onload = () => { this.images[i].loaded = true; };
    });
  }

  initEvents() {
    const onDown = (e) => {
      this.isDragging = true;
      this.startX = e.touches ? e.touches[0].clientX : e.clientX;
      this.startScroll = this.scroll.target;
      this.canvas.style.cursor = 'grabbing';
    };

    const onMove = (e) => {
      if (!this.isDragging) return;
      const x = e.touches ? e.touches[0].clientX : e.clientX;
      const dx = (this.startX - x) * (this.scrollSpeed * 0.8);
      this.scroll.target = this.startScroll + dx;
    };

    const onUp = () => {
      this.isDragging = false;
      this.canvas.style.cursor = 'grab';
    };

    this.canvas.addEventListener('mousedown', onDown);
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onUp);

    this.canvas.addEventListener('touchstart', onDown);
    window.addEventListener('touchmove', onMove);
    window.addEventListener('touchend', onUp);

    this.canvas.addEventListener('wheel', (e) => {
      this.scroll.target += (e.deltaY > 0 ? 1 : -1) * this.scrollSpeed * 35;
    });

    window.addEventListener('resize', () => this.resize());
  }

  resize() {
    this.width = this.container.clientWidth || window.innerWidth;
    this.height = this.container.clientHeight || 500;
    this.canvas.width = this.width;
    this.canvas.height = this.height;
  }

  animate() {
    this.scroll.current += (this.scroll.target - this.scroll.current) * this.scroll.ease;

    const ctx = this.ctx;
    ctx.clearRect(0, 0, this.width, this.height);

    if (!this.images.length) return;

    const cardW = Math.min(260, this.width * 0.28);
    const cardH = cardW * 1.25;
    const spacing = cardW + 40;
    const totalW = spacing * this.images.length;
    const centerY = this.height / 2 - 10;

    this.images.forEach((item, index) => {
      let x = (index * spacing - (this.scroll.current % totalW)) % totalW;
      if (x < -cardW) x += totalW;
      if (x > totalW - cardW) x -= totalW;

      const normX = (x - (this.width / 2 - cardW / 2)) / (this.width / 2);
      const bendY = Math.pow(normX, 2) * (this.bend * 45);
      const rotation = normX * 0.22;
      const scale = Math.max(0.75, 1 - Math.abs(normX) * 0.25);

      ctx.save();
      ctx.translate(x + cardW / 2, centerY + bendY);
      ctx.rotate(rotation);
      ctx.scale(scale, scale);

      ctx.shadowColor = 'rgba(0, 0, 0, 0.25)';
      ctx.shadowBlur = 20;
      ctx.shadowOffsetY = 10;

      ctx.fillStyle = '#ffffff';
      ctx.beginPath();
      ctx.roundRect(-cardW / 2, -cardH / 2, cardW, cardH, 18);
      ctx.fill();

      ctx.shadowColor = 'transparent';

      if (item.loaded) {
        ctx.save();
        ctx.beginPath();
        ctx.roundRect(-cardW / 2 + 10, -cardH / 2 + 10, cardW - 20, cardH - 55, 12);
        ctx.clip();
        ctx.drawImage(item.img, -cardW / 2 + 10, -cardH / 2 + 10, cardW - 20, cardH - 55);
        ctx.restore();
      }

      ctx.fillStyle = '#0f172a';
      ctx.font = 'bold 15px "Plus Jakarta Sans", sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText(item.text, 0, cardH / 2 - 18);

      ctx.restore();
    });

    requestAnimationFrame(() => this.animate());
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('topRecruitersCircularGallery');
  if (container) {
    new CircularGalleryEngine('topRecruitersCircularGallery', {
      items: [
        { image: 'assets/company_logos/logos/3.png', text: 'Infosys' },
        { image: 'assets/company_logos/logos/2.png', text: 'Cognizant' },
        { image: 'assets/company_logos/logos/1.png', text: 'BLUCONN' },
        { image: 'assets/company_logos/logos/4.png', text: 'TCS' },
        { image: 'assets/company_logos/logos/6.png', text: 'Akrivia HCM' },
        { image: 'assets/company_logos/logos/7.png', text: 'Boson' },
        { image: 'assets/company_logos/logos/12.png', text: 'Meeami' },
        { image: 'assets/company_logos/logos/9.png', text: 'intelliPaat' },
        { image: 'assets/company_logos/logos/8.png', text: 'SmartED' },
        { image: 'assets/company_logos/logos/11.png', text: 'Quanteon' },
        { image: 'assets/company_logos/logos/10.png', text: 'AteliaHealth' },
        { image: 'assets/company_logos/logos/15.png', text: 'Achala' }
      ],
      bend: 1.2,
      scrollSpeed: 2.2
    });
  }

  const internshipContainer = document.getElementById('internshipCompaniesCircularGallery');
  if (internshipContainer) {
    new CircularGalleryEngine('internshipCompaniesCircularGallery', {
      items: [
        { image: 'assets/company_logos/logos/3.png', text: 'Infosys' },
        { image: 'assets/company_logos/logos/2.png', text: 'Cognizant' },
        { image: 'assets/company_logos/logos/1.png', text: 'BLUCONN' },
        { image: 'assets/company_logos/logos/4.png', text: 'TCS' },
        { image: 'assets/company_logos/logos/6.png', text: 'Akrivia HCM' },
        { image: 'assets/company_logos/logos/7.png', text: 'Boson Tech' },
        { image: 'assets/company_logos/logos/12.png', text: 'Meeami Tech' },
        { image: 'assets/company_logos/logos/9.png', text: 'intelliPaat' },
        { image: 'assets/company_logos/logos/8.png', text: 'SmartED' },
        { image: 'assets/company_logos/logos/11.png', text: 'Quanteon Labs' },
        { image: 'assets/company_logos/logos/10.png', text: 'AteliaHealth' },
        { image: 'assets/company_logos/logos/15.png', text: 'Achala IT' }
      ],
      bend: 1.2,
      scrollSpeed: 2.2
    });
  }
});
