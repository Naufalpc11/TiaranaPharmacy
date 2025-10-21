import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export const initializeContactAnimations = (refs = {}) => {
  const {
    heroOverlay,
    heroTitle,
    heroSubtitle,
    formCard,
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

  if (formCard) {
    gsap.from(formCard, {
      scrollTrigger: {
        trigger: formCard,
        start: 'top 85%',
        toggleActions: 'play none none reverse',
      },
      y: 60,
      autoAlpha: 0,
      duration: 0.9,
      ease: 'power3.out',
    });

    const fields = gsap.utils.toArray(
      formCard.querySelectorAll('.contact-form__field')
    );

    if (fields.length) {
      gsap.from(fields, {
        scrollTrigger: {
          trigger: formCard,
          start: 'top 80%',
          toggleActions: 'play none none reverse',
        },
        y: 30,
        autoAlpha: 0,
        duration: 0.6,
        stagger: {
          each: 0.1,
          from: 'start',
        },
        ease: 'power2.out',
      });
    }
  }
};
