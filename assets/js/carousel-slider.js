/**
 * ReactBits Carousel Component JS Implementation
 */
document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.carousel-container');
  const track = document.querySelector('.carousel-track');
  if (!container || !track) return;

  const items = Array.from(track.querySelectorAll('.carousel-item'));
  const indicators = Array.from(document.querySelectorAll('.carousel-indicator'));
  if (items.length === 0) return;

  const baseWidth = 360;
  const containerPadding = 24;
  const itemWidth = baseWidth - containerPadding * 2; // 312px
  const gap = 20;
  const trackItemOffset = itemWidth + gap;

  let currentPosition = 0;
  let isDragging = false;
  let startX = 0;
  let currentTranslate = 0;
  let prevTranslate = 0;
  let animationId = 0;

  // Set Item Widths
  items.forEach(item => {
    item.style.width = `${itemWidth}px`;
    item.style.height = '310px';
  });

  const updateCarousel = () => {
    currentTranslate = -currentPosition * trackItemOffset;
    prevTranslate = currentTranslate;
    track.style.transform = `translateX(${currentTranslate}px)`;

    // Update 3D rotateY per item
    items.forEach((item, index) => {
      const diff = index - currentPosition;
      let rotateY = 0;
      if (diff > 0) rotateY = -25 * Math.min(diff, 2);
      else if (diff < 0) rotateY = 25 * Math.min(Math.abs(diff), 2);
      
      item.style.transform = `rotateY(${rotateY}deg)`;
    });

    // Update Active Indicator
    indicators.forEach((ind, idx) => {
      if (idx === currentPosition) ind.classList.add('active');
      else ind.classList.remove('active');
    });
  };

  // Click Indicator Navigation
  indicators.forEach((ind, idx) => {
    ind.addEventListener('click', () => {
      currentPosition = idx;
      updateCarousel();
    });
  });

  // Touch & Mouse Drag Handlers
  const dragStart = (e) => {
    isDragging = true;
    startX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
    animationId = requestAnimationFrame(animation);
    track.style.cursor = 'grabbing';
  };

  const dragMove = (e) => {
    if (!isDragging) return;
    const currentX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
    const deltaX = currentX - startX;
    currentTranslate = prevTranslate + deltaX;
  };

  const dragEnd = () => {
    if (!isDragging) return;
    isDragging = false;
    cancelAnimationFrame(animationId);
    track.style.cursor = 'grab';

    const movedBy = currentTranslate - prevTranslate;
    if (movedBy < -50 && currentPosition < items.length - 1) {
      currentPosition += 1;
    } else if (movedBy > 50 && currentPosition > 0) {
      currentPosition -= 1;
    }

    updateCarousel();
  };

  const animation = () => {
    if (isDragging) {
      track.style.transform = `translateX(${currentTranslate}px)`;
      requestAnimationFrame(animation);
    }
  };

  // Event Listeners
  track.addEventListener('mousedown', dragStart);
  track.addEventListener('mousemove', dragMove);
  window.addEventListener('mouseup', dragEnd);

  track.addEventListener('touchstart', dragStart);
  track.addEventListener('touchmove', dragMove);
  window.addEventListener('touchend', dragEnd);

  // Initial Run
  updateCarousel();
});
