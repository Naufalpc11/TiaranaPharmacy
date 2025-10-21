import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export const initializeArtikelAnimations = (refs = {}) => {
  const {
    heroOverlay,
    heroTitle,
    heroSubtitle,
    searchBar,
    artikelGrid,
  } = refs;

  const heroTimeline = gsap.timeline({
    defaults: { ease: 'power3.out' },
  });

  if (heroOverlay) {
    heroTimeline.from(heroOverlay, {
      autoAlpha: 0,
      duration: 1.2,
    });
  }

  if (heroTitle) {
    heroTimeline.from(
      heroTitle,
      {
        y: 80,
        autoAlpha: 0,
        duration: 1,
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
        duration: 0.9,
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
        duration: 0.6,
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
          start: 'top 80%',
          toggleActions: 'play none none reverse',
        },
        y: 60,
        autoAlpha: 0,
        duration: 0.8,
        ease: 'power3.out',
        stagger: {
          each: 0.15,
          from: 'start',
        },
      });
    }
  }
};
