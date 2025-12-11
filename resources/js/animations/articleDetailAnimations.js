import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const shouldSkipAnimations = () => {
  if (typeof window === 'undefined') return false;
  const prefersReduce =
    typeof matchMedia !== 'undefined' &&
    matchMedia('(prefers-reduced-motion: reduce)').matches;
  const isCoarse =
    (typeof matchMedia !== 'undefined' && matchMedia('(pointer: coarse)').matches) ||
    'ontouchstart' in window ||
    (navigator?.maxTouchPoints ?? 0) > 0;
  return prefersReduce || isCoarse;
};

export const initializeArticleDetailAnimations = (refs = {}) => {
  const {
    heroOverlay,
    heroTitle,
    heroDate,
    heroBackButton,
    contentCard,
  } = refs;

  const scheduleRefresh = () => {
    requestAnimationFrame(() => ScrollTrigger.refresh());
    setTimeout(() => ScrollTrigger.refresh(), 300);
    setTimeout(() => ScrollTrigger.refresh(), 800);
  };

  if (shouldSkipAnimations()) {
    return;
  }

  if (ScrollTrigger.normalizeScroll) {
    ScrollTrigger.normalizeScroll(false);
  }

  const heroTimeline = gsap.timeline({
    defaults: { ease: 'power3.out' },
  });

  if (heroOverlay) {
    heroTimeline.from(heroOverlay, {
      autoAlpha: 0,
      duration: 1,
    });
  }

  if (heroBackButton) {
    heroTimeline.from(
      heroBackButton,
      {
        y: 20,
        autoAlpha: 0,
        duration: 0.5,
      },
      '-=0.8'
    );
  }

  if (heroTitle) {
    heroTimeline.from(
      heroTitle,
      {
        y: 80,
        autoAlpha: 0,
        duration: 0.85,
        ease: 'power4.out',
      },
      '-=0.6'
    );
  }

  if (heroDate) {
    heroTimeline.from(
      heroDate,
      {
        y: 40,
        autoAlpha: 0,
        duration: 0.55,
      },
      '-=0.5'
    );
  }

  if (contentCard) {
    gsap.from(contentCard, {
      scrollTrigger: {
        trigger: contentCard,
        start: 'top 80%',
        once: true,
      },
      y: 60,
      autoAlpha: 0,
      duration: 0.75,
      ease: 'power3.out',
    });

    const contentBlocks = contentCard.querySelectorAll(
      '.article-detail-paragraph, .article-detail-list'
    );

    if (contentBlocks.length) {
      gsap.from(contentBlocks, {
        scrollTrigger: {
          trigger: contentCard,
          start: 'top 70%',
          once: true,
        },
        y: 20,
        autoAlpha: 0,
        duration: 0.5,
        stagger: {
          each: 0.08,
          from: 'start',
        },
        ease: 'power2.out',
      });
    }
  }

  scheduleRefresh();
};
