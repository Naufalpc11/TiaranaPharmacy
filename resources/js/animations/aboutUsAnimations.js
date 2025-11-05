import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { TextPlugin } from 'gsap/TextPlugin';

gsap.registerPlugin(ScrollTrigger, TextPlugin);

export const initializeAboutUsAnimations = () => {

  const heroTimeline = gsap.timeline({
    defaults: { ease: 'power3.out' }
  });

  heroTimeline
    .from('.about-header-overlay', {
      opacity: 0,
      duration: 1.5
    })
    .from('.about-header h1', {
      y: 100,
      opacity: 0,
      duration: 1.2,
      ease: 'power4.out'
    }, '-=0.8')
    .from('.about-header p', {
      y: 50,
      opacity: 0,
      duration: 1
    }, '-=0.7');

  const aboutContentTimeline = gsap.timeline({
    scrollTrigger: {
      trigger: '.about-content',
      start: 'top 75%',
      once: true
    }
  });

  aboutContentTimeline.from('.about-section p', {
    y: 50,
    opacity: 0,
    duration: 1,
    ease: 'power3.out'
  });

  const historySection = document.querySelector('.history-section');
  if (historySection) {
    const historySectionTimeline = gsap.timeline({
      scrollTrigger: {
        trigger: '.history-section',
        start: 'top 70%',
        once: true
      }
    });

    historySectionTimeline
      .from('.history-content h2', {
        y: 50,
        opacity: 0,
        duration: 0.8,
        ease: 'back.out(1.7)'
      })
      .from('.history-content p', {
        opacity: 0,
        duration: 1,
        y: 30,
        ease: 'power3.out'
      }, '-=0.4');
  }

  // Team section title animation
  gsap.from('.team-section h2', {
    scrollTrigger: {
      trigger: '.team-section',
      start: 'top 75%',
      once: true
    },
    y: 30,
    opacity: 0,
    duration: 0.8,
    ease: 'back.out(1.7)'
  });

  // Pharmacist card reveal
  gsap.from('.pharmacist-card', {
    scrollTrigger: {
      trigger: '.pharmacist-card',
      start: 'top 80%',
      once: true
    },
    y: 60,
    opacity: 0,
    duration: 1,
    ease: 'power3.out'
  });

  // Light stagger for values and stats
  gsap.from('.values-section .mission-card', {
    scrollTrigger: {
      trigger: '.values-section',
      start: 'top 75%',
      once: true
    },
    y: 40,
    opacity: 0,
    duration: 0.8,
    stagger: { each: 0.15, ease: 'power3.out' },
    clearProps: 'transform,opacity'
  });

  gsap.from('.history-stats .history-stat-card', {
    scrollTrigger: {
      trigger: '.history-section',
      start: 'top 75%',
      once: true
    },
    y: 40,
    opacity: 0,
    duration: 0.8,
    stagger: { each: 0.15, ease: 'power3.out' },
    clearProps: 'transform,opacity'
  });

  // Location section animations
  const locationTimeline = gsap.timeline({
    scrollTrigger: {
      trigger: '.location-section',
      start: 'top 70%',
      once: true
    }
  });

  locationTimeline
    .from('.location-section h2', {
      y: 30,
      opacity: 0,
      duration: 0.8,
      ease: 'back.out(1.7)'
    })
    .from('.contact-info-card', {
      x: -50,
      opacity: 0,
      duration: 0.8,
      stagger: {
        each: 0.2,
        ease: 'power3.out'
      }
    }, '-=0.4')
    .from('.map-container', {
      scale: 0.9,
      opacity: 0,
      duration: 1,
      ease: 'power3.out'
    }, '-=0.6');

  // Add hover effect for contact items
  const contactItems = document.querySelectorAll('.contact-info-card');
  contactItems.forEach(item => {
    item.addEventListener('mouseenter', () => {
      gsap.to(item, {
        scale: 1.02,
        y: -5,
        duration: 0.3,
        ease: 'power2.out'
      });
    });

    item.addEventListener('mouseleave', () => {
      gsap.to(item, {
        scale: 1,
        y: 0,
        duration: 0.3,
        ease: 'power2.out'
      });
    });
  });
};
