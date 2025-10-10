import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { TextPlugin } from 'gsap/TextPlugin';

gsap.registerPlugin(ScrollTrigger, TextPlugin);

export const initializeAboutUsAnimations = () => {
  // Initial hero section animation
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
  // About content section reveal
  const aboutContentTimeline = gsap.timeline({
    scrollTrigger: {
      trigger: '.about-content',
      start: 'top 75%',
      toggleActions: 'play none none reverse'
    }
  });

  aboutContentTimeline.from('.about-section p', {
    y: 50,
    opacity: 0,
    duration: 1,
    ease: 'power3.out'
  });

  // History section animation with split text effect
  const historySection = document.querySelector('.history-section');
  if (historySection) {
    const historySectionTimeline = gsap.timeline({
      scrollTrigger: {
        trigger: '.history-section',
        start: 'top 70%',
        toggleActions: 'play none none reverse'
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

  // Team section title animation
  gsap.from('.team-section h2', {
    scrollTrigger: {
      trigger: '.team-section',
      start: 'top 75%',
      toggleActions: 'play none none reverse'
    },
    y: 30,
    opacity: 0,
    duration: 0.8,
    ease: 'back.out(1.7)'
  });

  // Team members stagger animation with hover effect
  const teamMembers = document.querySelectorAll('.team-member');
  gsap.from(teamMembers, {
    scrollTrigger: {
      trigger: '.team-grid',
      start: 'top 70%',
      toggleActions: 'play none none reverse'
    },
    y: 100,
    opacity: 0,
    duration: 1,
    stagger: {
      each: 0.2,
      ease: 'power3.out'
    }
  });

  // Add hover animations for team members
  teamMembers.forEach(member => {
    const image = member.querySelector('img');
    const info = member.querySelectorAll('h3, p');

    member.addEventListener('mouseenter', () => {
      gsap.to(image, {
        scale: 1.1,
        duration: 0.3,
        ease: 'power2.out'
      });
      gsap.to(info, {
        y: -5,
        duration: 0.3,
        stagger: 0.1,
        ease: 'power2.out'
      });
    });

    member.addEventListener('mouseleave', () => {
      gsap.to(image, {
        scale: 1,
        duration: 0.3,
        ease: 'power2.out'
      });
      gsap.to(info, {
        y: 0,
        duration: 0.3,
        stagger: 0.1,
        ease: 'power2.out'
      });
    });
  });

  // Location section animations
  const locationTimeline = gsap.timeline({
    scrollTrigger: {
      trigger: '.location-section',
      start: 'top 70%',
      toggleActions: 'play none none reverse'
    }
  });

  locationTimeline
    .from('.location-section h2', {
      y: 30,
      opacity: 0,
      duration: 0.8,
      ease: 'back.out(1.7)'
    })
    .from('.contact-item', {
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
  const contactItems = document.querySelectorAll('.contact-item');
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
}
