import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

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

  // Hero section animation
  gsap.set(heroContent, {
    autoAlpha: 0,
    scale: 0.9
  });

  gsap.set([heroTitle, heroSubtitle1, heroSubtitle2], {
    autoAlpha: 0,
    y: 30
  });

  const heroTimeline = gsap.timeline({
    defaults: {
      ease: 'power3.out'
    }
  });

  heroTimeline
    .to(heroContent, {
      autoAlpha: 1,
      scale: 1,
      duration: 1,
      ease: 'power2.out'
    })
    .to(heroTitle, {
      autoAlpha: 1,
      y: 0,
      duration: 0.8
    }, '-=0.3')
    .to(heroSubtitle1, {
      autoAlpha: 1,
      y: 0,
      duration: 0.6
    }, '-=0.4')
    .to(heroSubtitle2, {
      autoAlpha: 1,
      y: 0,
      duration: 0.6
    }, '-=0.4');

  // Features grid animation
  gsap.set(featuresGrid.children, {
    autoAlpha: 0,
    y: 50
  });

  gsap.to(featuresGrid.children, {
    scrollTrigger: {
      trigger: featuresGrid,
      start: 'top 90%',
      end: 'top 60%',
      toggleActions: 'play reverse play reverse',
      scrub: 0.5
    },
    y: 0,
    autoAlpha: 1,
    duration: 0.5,
    stagger: 0.1
  });

  // About section animations
  gsap.set(aboutText, {
    autoAlpha: 0,
    x: -50
  });

  gsap.set(aboutImage, {
    autoAlpha: 0,
    x: 50
  });

  gsap.to(aboutText, {
    scrollTrigger: {
      trigger: aboutSection,
      start: 'top 100%',
      end: 'top 50%',
      toggleActions: 'play reverse play reverse',
      scrub: 0.5
    },
    x: 0,
    autoAlpha: 1,
    duration: 0.5
  });

  gsap.to(aboutImage, {
    scrollTrigger: {
      trigger: aboutSection,
      start: 'top 90%',
      end: 'top 50%',
      toggleActions: 'play reverse play reverse',
      scrub: 0.5
    },
    x: 0,
    autoAlpha: 1,
    duration: 0.5
  });

  // About features animation
  gsap.set(aboutFeatures.children, {
    autoAlpha: 0,
    y: 30
  });

  gsap.to(aboutFeatures.children, {
    scrollTrigger: {
      trigger: aboutFeatures,
      start: 'top 90%',
      end: 'top 50%',
      toggleActions: 'play reverse play reverse',
      scrub: 0.5
    },
    y: 0,
    autoAlpha: 1,
    duration: 0.4,
    stagger: {
      each: 0.1,
      from: "start"
    }
  });

  // Services section animations
  gsap.set(servicesTitle, {
    y: 30,
    opacity: 0
  });

  gsap.to(servicesTitle, {
    scrollTrigger: {
      trigger: servicesSection,
      start: 'top 90%',
      end: 'top 20%',
      toggleActions: 'play reverse play reverse',
      scrub: 1
    },
    y: 0,
    opacity: 1,
    duration: 0.8
  });

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
        start: 'top 90%',
        end: 'top 20%',
        scrub: 1,
        toggleActions: 'play reverse play reverse'
      },
      x: 0,
      opacity: 1,
      scale: 1,
      duration: 1.5,
      ease: 'power2.out'
    });
  });
};