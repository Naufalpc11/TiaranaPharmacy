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

export const initializeHomeAnimations = (refs) => {
  const {
    heroContent,
    heroTitle,
    heroSubtitle1,
    heroSubtitle2,
    featuresGrid,
    aboutSection,
    aboutText,
    aboutImage,
    aboutFeatures,
    servicesSection,
    servicesTitle,
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

  // Hero section animation mirrors About Us hero sequence
  const heroTimeline = gsap.timeline({
    defaults: {
      ease: 'power3.out'
    }
  });

  heroTimeline
    .from(heroContent, {
      autoAlpha: 0,
      duration: 1.1
    })
    .from(heroTitle, {
      y: 100,
      autoAlpha: 0,
      duration: 0.9,
      ease: 'power4.out'
    }, '-=0.8')
    .from(
      [heroSubtitle1, heroSubtitle2].filter(Boolean),
      {
        y: 50,
        autoAlpha: 0,
        duration: 0.7,
        stagger: 0.2
      },
      '-=0.7'
    );

  // Features grid animation
  if (featuresGrid && featuresGrid.children.length) {
    const featureItems = Array.from(featuresGrid.children);

    gsap.set(featureItems, {
      autoAlpha: 0,
      y: 50
    });

    gsap.to(featureItems, {
      scrollTrigger: {
        trigger: featuresGrid,
        start: 'top 80%',
        once: true
      },
      y: 0,
      autoAlpha: 1,
      duration: 0.5,
      stagger: 0.12,
      ease: 'power2.out'
    });
  }

  // About section animations
  if (aboutSection) {
    if (aboutText) {
      gsap.set(aboutText, {
        autoAlpha: 0,
        x: -50
      });

      gsap.to(aboutText, {
        scrollTrigger: {
          trigger: aboutSection,
          start: 'top 80%',
          once: true
        },
        x: 0,
        autoAlpha: 1,
        duration: 0.5,
        ease: 'power2.out'
      });
    }

    if (aboutImage) {
      gsap.set(aboutImage, {
        autoAlpha: 0,
        x: 50
      });

      gsap.to(aboutImage, {
        scrollTrigger: {
          trigger: aboutSection,
          start: 'top 80%',
          once: true
        },
        x: 0,
        autoAlpha: 1,
        duration: 0.5,
        ease: 'power2.out'
      });
    }
  }

  // About features animation
  if (aboutFeatures && aboutFeatures.children.length) {
    const featureItems = Array.from(aboutFeatures.children);

    gsap.set(featureItems, {
      autoAlpha: 0,
      y: 30
    });

    gsap.to(featureItems, {
      scrollTrigger: {
        trigger: aboutFeatures,
        start: 'top 80%',
        once: true
      },
      y: 0,
      autoAlpha: 1,
      duration: 0.45,
      stagger: {
        each: 0.1,
        from: 'start'
      },
      ease: 'power2.out'
    });
  }

  // Services section animations
  if (servicesSection && servicesTitle) {
    gsap.set(servicesTitle, {
      y: 30,
      opacity: 0
    });

    gsap.to(servicesTitle, {
      scrollTrigger: {
        trigger: servicesSection,
        start: 'top 80%',
        once: true
      },
      y: 0,
      opacity: 1,
      duration: 0.55,
      ease: 'power2.out'
    });
  }

  // Animate each service card
  const serviceCards = document.querySelectorAll('.service-card');
  serviceCards.forEach((card, index) => {
    const direction = index % 2 === 0 ? -1 : 1;

    // Set initial state
    gsap.set(card, {
      x: 100 * direction,
      opacity: 0,
      scale: 0.8
    });

    // Create scroll trigger animation
    gsap.to(card, {
      scrollTrigger: {
        trigger: card,
        start: 'top 80%',
        once: true
      },
      x: 0,
      opacity: 1,
      scale: 1,
      duration: 0.8,
      ease: 'power2.out'
    });
  });

  scheduleRefresh();
};
