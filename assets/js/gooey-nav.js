// Official ReactBits GooeyNav Vanilla Engine
// Implements active pill sliding, text morphing, and particle explosion bursts

class GooeyNavEngine {
  constructor(containerId, options = {}) {
    this.container = typeof containerId === 'string' ? document.getElementById(containerId) : containerId;
    if (!this.container) return;

    this.animationTime = options.animationTime || 600;
    this.particleCount = options.particleCount || 15;
    this.particleDistances = options.particleDistances || [90, 10];
    this.particleR = options.particleR || 100;
    this.timeVariance = options.timeVariance || 300;
    this.colors = options.colors || [1, 2, 3, 1, 2, 3, 1, 4];
    this.initialActiveIndex = options.initialActiveIndex || 0;

    this.nav = this.container.querySelector('nav');
    this.ul = this.container.querySelector('ul');
    this.filter = this.container.querySelector('.effect.filter');
    this.text = this.container.querySelector('.effect.text');
    this.lis = Array.from(this.ul.querySelectorAll('li'));

    this.activeIndex = this.initialActiveIndex;
    this.init();
  }

  noise(n = 1) {
    return n / 2 - Math.random() * n;
  }

  getXY(distance, pointIndex, totalPoints) {
    const angle = ((360 + this.noise(8)) / totalPoints) * pointIndex * (Math.PI / 180);
    return [distance * Math.cos(angle), distance * Math.sin(angle)];
  }

  createParticle(i, t, d, r) {
    let rotate = this.noise(r / 10);
    return {
      start: this.getXY(d[0], this.particleCount - i, this.particleCount),
      end: this.getXY(d[1] + this.noise(7), this.particleCount - i, this.particleCount),
      time: t,
      scale: 1 + this.noise(0.2),
      color: this.colors[Math.floor(Math.random() * this.colors.length)],
      rotate: rotate > 0 ? (rotate + r / 20) * 10 : (rotate - r / 20) * 10
    };
  }

  makeParticles(element) {
    const d = this.particleDistances;
    const r = this.particleR;
    const bubbleTime = this.animationTime * 2 + this.timeVariance;
    element.style.setProperty('--time', `${bubbleTime}ms`);

    for (let i = 0; i < this.particleCount; i++) {
      const t = this.animationTime * 2 + this.noise(this.timeVariance * 2);
      const p = this.createParticle(i, t, d, r);
      element.classList.remove('active');

      setTimeout(() => {
        const particle = document.createElement('span');
        const point = document.createElement('span');
        particle.classList.add('particle');
        particle.style.setProperty('--start-x', `${p.start[0]}px`);
        particle.style.setProperty('--start-y', `${p.start[1]}px`);
        particle.style.setProperty('--end-x', `${p.end[0]}px`);
        particle.style.setProperty('--end-y', `${p.end[1]}px`);
        particle.style.setProperty('--time', `${p.time}ms`);
        particle.style.setProperty('--scale', `${p.scale}`);
        particle.style.setProperty('--color', `var(--color-${p.color}, #6366f1)`);
        particle.style.setProperty('--rotate', `${p.rotate}deg`);

        point.classList.add('point');
        particle.appendChild(point);
        element.appendChild(particle);

        requestAnimationFrame(() => {
          element.classList.add('active');
        });

        setTimeout(() => {
          try {
            element.removeChild(particle);
          } catch {
            // Ignore if removed
          }
        }, t);
      }, 30);
    }
  }

  updateEffectPosition(element) {
    if (!this.container || !this.filter || !this.text || !element) return;
    const containerRect = this.container.getBoundingClientRect();
    const pos = element.getBoundingClientRect();

    const styles = {
      left: `${pos.x - containerRect.x}px`,
      top: `${pos.y - containerRect.y}px`,
      width: `${pos.width}px`,
      height: `${pos.height}px`
    };

    Object.assign(this.filter.style, styles);
    Object.assign(this.text.style, styles);
    this.text.innerText = element.innerText;
  }

  handleClick(liEl, index) {
    if (this.activeIndex === index) return;

    this.lis.forEach(li => li.classList.remove('active'));
    liEl.classList.add('active');
    this.activeIndex = index;

    this.updateEffectPosition(liEl);

    if (this.filter) {
      const particles = this.filter.querySelectorAll('.particle');
      particles.forEach(p => this.filter.removeChild(p));
    }

    if (this.text) {
      this.text.classList.remove('active');
      void this.text.offsetWidth;
      this.text.classList.add('active');
    }

    if (this.filter) {
      this.makeParticles(this.filter);
    }
  }

  init() {
    this.lis.forEach((li, index) => {
      // Find current page active tab
      const link = li.querySelector('a');
      const href = link ? link.getAttribute('href') : '';
      const currentPage = window.location.pathname.split('/').pop() || 'index.php';

      if (href && (currentPage === href || (currentPage === '' && href === 'index.php'))) {
        this.activeIndex = index;
      }

      li.addEventListener('click', (e) => {
        this.handleClick(li, index);
      });
    });

    const activeLi = this.lis[this.activeIndex] || this.lis[0];
    if (activeLi) {
      activeLi.classList.add('active');
      this.updateEffectPosition(activeLi);
      this.text.classList.add('active');
    }

    const resizeObserver = new ResizeObserver(() => {
      const currentActiveLi = this.lis[this.activeIndex];
      if (currentActiveLi) {
        this.updateEffectPosition(currentActiveLi);
      }
    });

    resizeObserver.observe(this.container);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.gooey-nav-container');
  if (container) {
    window.gooeyNavInstance = new GooeyNavEngine(container, {
      particleCount: 12,
      particleDistances: [90, 10],
      particleR: 100,
      animationTime: 600,
      timeVariance: 300,
      colors: [1, 2, 3, 1, 2, 3, 1, 4]
    });
  }
});
