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

export const initializeContactAnimations = (refs = {}) => {
  const {
    heroOverlay,
    heroTitle,
    heroSubtitle,
    formCard,
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

  if (formCard) {
    gsap.from(formCard, {
      scrollTrigger: {
        trigger: formCard,
        start: 'top 80%',
        once: true,
      },
      y: 60,
      autoAlpha: 0,
      duration: 0.65,
      ease: 'power3.out',
    });

    const fields = gsap.utils.toArray(
      formCard.querySelectorAll('.contact-form__field')
    );

    if (fields.length) {
      gsap.from(fields, {
        scrollTrigger: {
          trigger: formCard,
          start: 'top 75%',
          once: true,
        },
        y: 30,
        autoAlpha: 0,
        duration: 0.5,
        stagger: {
          each: 0.1,
          from: 'start',
        },
        ease: 'power2.out',
      });
    }
  }

  scheduleRefresh();
};
