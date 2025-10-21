import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export const initializeArticleDetailAnimations = (refs = {}) => {
  const {
    heroOverlay,
    heroTitle,
    heroDate,
    heroBackButton,
    contentCard,
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

  if (heroBackButton) {
    heroTimeline.from(
      heroBackButton,
      {
        y: 20,
        autoAlpha: 0,
        duration: 0.6,
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
        duration: 1,
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
        duration: 0.7,
      },
      '-=0.5'
    );
  }

  if (contentCard) {
    gsap.from(contentCard, {
      scrollTrigger: {
        trigger: contentCard,
        start: 'top 85%',
        toggleActions: 'play none none reverse',
      },
      y: 60,
      autoAlpha: 0,
      duration: 1,
      ease: 'power3.out',
    });

    const contentBlocks = contentCard.querySelectorAll(
      '.article-detail-paragraph, .article-detail-list'
    );

    if (contentBlocks.length) {
      gsap.from(contentBlocks, {
        scrollTrigger: {
          trigger: contentCard,
          start: 'top 75%',
          toggleActions: 'play none none reverse',
        },
        y: 20,
        autoAlpha: 0,
        duration: 0.6,
        stagger: {
          each: 0.08,
          from: 'start',
        },
        ease: 'power2.out',
      });
    }
  }
};
