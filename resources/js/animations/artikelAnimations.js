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

export const initializeArtikelAnimations = (refs = {}) => {
  const {
    heroOverlay,
    heroTitle,
    heroSubtitle,
    searchBar,
    artikelGrid,
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

  if (heroTitle) {
    heroTimeline.from(
      heroTitle,
      {
        y: 80,
        autoAlpha: 0,
        duration: 0.85,
        ease: 'power4.out',
      },
      heroOverlay ? '-=0.7' : 0
    );
  }

  if (heroSubtitle) {
    heroTimeline.from(
      heroSubtitle,
      {
        y: 40,
        autoAlpha: 0,
        duration: 0.75,
      },
      '-=0.6'
    );
  }

  if (searchBar) {
    heroTimeline.from(
      searchBar,
      {
        y: 20,
        autoAlpha: 0,
        duration: 0.5,
      },
      '-=0.5'
    );
  }

  if (artikelGrid) {
    const cards = gsap.utils.toArray(
      artikelGrid.querySelectorAll('.article-card')
    );

    if (cards.length) {
      gsap.from(cards, {
        scrollTrigger: {
          trigger: artikelGrid,
          start: 'top 75%',
          once: true,
        },
        y: 60,
        autoAlpha: 0,
        duration: 0.65,
        ease: 'power3.out',
        stagger: {
          each: 0.15,
          from: 'start',
        },
        clearProps: 'transform,opacity',
      });
    }
  }

  scheduleRefresh();
};
