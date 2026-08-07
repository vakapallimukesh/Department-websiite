/**
 * ReactBits ScrollStack Component JS Implementation (Window Scroll Pin & Scale Stack)
 */
document.addEventListener('DOMContentLoaded', () => {
  const scroller = document.querySelector('.scroll-stack-scroller');
  if (!scroller) return;

  const cards = Array.from(document.querySelectorAll('.scroll-stack-card'));
  const endElement = document.querySelector('.scroll-stack-end');
  if (cards.length === 0) return;

  const itemScale = 0.03;
  const itemStackDistance = 25;
  const baseScale = 0.88;
  const stackPositionPercent = 0.12; // 12% top of viewport

  let cardInitialTops = [];
  let endElementTop = 0;

  const measurePositions = () => {
    // Temporarily reset transforms to measure clean static layout positions
    const originalTransforms = cards.map(card => card.style.transform);
    cards.forEach(card => {
      card.style.transform = 'none';
    });

    const scrollY = window.scrollY || document.documentElement.scrollTop;

    cardInitialTops = cards.map(card => {
      const rect = card.getBoundingClientRect();
      return rect.top + scrollY;
    });

    if (endElement) {
      const rect = endElement.getBoundingClientRect();
      endElementTop = rect.top + scrollY;
    } else if (cardInitialTops.length > 0) {
      endElementTop = cardInitialTops[cardInitialTops.length - 1] + 400;
    }

    // Restore transforms
    cards.forEach((card, idx) => {
      card.style.transform = originalTransforms[idx];
    });
  };

  const calculateProgress = (scrollTop, start, end) => {
    if (scrollTop < start) return 0;
    if (scrollTop > end) return 1;
    return (scrollTop - start) / (end - start);
  };

  let ticking = false;

  const updateCardTransforms = () => {
    if (cardInitialTops.length !== cards.length) {
      measurePositions();
    }

    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    const containerHeight = window.innerHeight;
    const stackPositionPx = containerHeight * stackPositionPercent;
    const scaleEndPositionPx = containerHeight * 0.05;

    cards.forEach((card, i) => {
      const cardTop = cardInitialTops[i];
      const triggerStart = cardTop - stackPositionPx - itemStackDistance * i;
      const triggerEnd = cardTop - scaleEndPositionPx;
      const pinStart = cardTop - stackPositionPx - itemStackDistance * i;
      const pinEnd = endElementTop - containerHeight / 2;

      const scaleProgress = calculateProgress(scrollTop, triggerStart, triggerEnd);
      const targetScale = baseScale + i * itemScale;
      const scale = 1 - scaleProgress * (1 - targetScale);

      let translateY = 0;
      const isPinned = scrollTop >= pinStart && scrollTop <= pinEnd;

      if (isPinned) {
        translateY = scrollTop - cardTop + stackPositionPx + itemStackDistance * i;
      } else if (scrollTop > pinEnd) {
        translateY = pinEnd - cardTop + stackPositionPx + itemStackDistance * i;
      }

      const clampedScale = Math.max(0.8, Math.min(1, scale));
      card.style.transform = `translate3d(0, ${Math.round(translateY * 10) / 10}px, 0) scale(${Math.round(clampedScale * 1000) / 1000})`;
      card.style.zIndex = String(10 + i);
    });

    ticking = false;
  };

  const onScroll = () => {
    if (!ticking) {
      window.requestAnimationFrame(updateCardTransforms);
      ticking = true;
    }
  };

  const onResize = () => {
    measurePositions();
    updateCardTransforms();
  };

  // Initial measurement and rendering
  measurePositions();
  updateCardTransforms();

  // Scroll and Resize Event Listeners
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onResize);
});
