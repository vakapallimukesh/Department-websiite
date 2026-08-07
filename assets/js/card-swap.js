/**
 * ReactBits CardSwap Component JS Implementation (GSAP Engine)
 */
document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.card-swap-container');
  if (!container || typeof gsap === 'undefined') return;

  const cards = Array.from(container.querySelectorAll('.card-swap-item'));
  if (cards.length === 0) return;

  const cardDistance = 45;
  const verticalDistance = 55;
  const delay = 4000;
  const skewAmount = 4;
  const total = cards.length;

  const config = {
    ease: 'elastic.out(0.6, 0.9)',
    durDrop: 1.8,
    durMove: 1.8,
    durReturn: 1.8,
    promoteOverlap: 0.9,
    returnDelay: 0.05
  };

  let order = Array.from({ length: total }, (_, i) => i);
  let intervalId = null;
  let currentTl = null;

  const makeSlot = (i, distX, distY, totalCount) => ({
    x: i * distX,
    y: -i * distY,
    z: -i * distX * 1.5,
    zIndex: totalCount - i
  });

  const placeNow = (el, slot, skew) => {
    gsap.set(el, {
      x: slot.x,
      y: slot.y,
      z: slot.z,
      xPercent: -50,
      yPercent: -50,
      skewY: skew,
      transformOrigin: 'center center',
      zIndex: slot.zIndex,
      force3D: true
    });
  };

  // Initial Placement
  cards.forEach((card, i) => {
    placeNow(card, makeSlot(i, cardDistance, verticalDistance, total), skewAmount);
  });

  const swap = () => {
    if (order.length < 2) return;

    const [front, ...rest] = order;
    const elFront = cards[front];
    const tl = gsap.timeline();
    currentTl = tl;

    tl.to(elFront, {
      y: '+=450',
      duration: config.durDrop,
      ease: config.ease
    });

    tl.addLabel('promote', `-=${config.durDrop * config.promoteOverlap}`);
    rest.forEach((idx, i) => {
      const el = cards[idx];
      const slot = makeSlot(i, cardDistance, verticalDistance, total);
      tl.set(el, { zIndex: slot.zIndex }, 'promote');
      tl.to(
        el,
        {
          x: slot.x,
          y: slot.y,
          z: slot.z,
          duration: config.durMove,
          ease: config.ease
        },
        `promote+=${i * 0.12}`
      );
    });

    const backSlot = makeSlot(total - 1, cardDistance, verticalDistance, total);
    tl.addLabel('return', `promote+=${config.durMove * config.returnDelay}`);
    
    tl.call(() => {
      gsap.set(elFront, { zIndex: backSlot.zIndex });
    }, null, 'return');

    tl.to(
      elFront,
      {
        x: backSlot.x,
        y: backSlot.y,
        z: backSlot.z,
        duration: config.durReturn,
        ease: config.ease
      },
      'return'
    );

    tl.call(() => {
      order = [...rest, front];
    });
  };

  // Start Interval
  intervalId = setInterval(swap, delay);

  // Pause on hover
  container.addEventListener('mouseenter', () => {
    if (currentTl) currentTl.pause();
    clearInterval(intervalId);
  });

  container.addEventListener('mouseleave', () => {
    if (currentTl) currentTl.play();
    intervalId = setInterval(swap, delay);
  });
});
