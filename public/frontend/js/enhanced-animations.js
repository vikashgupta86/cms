/**
 * Enhanced Animations and Interactions
 * AYUSH Department Website
 * Professional Government Portal UI/UX Enhancement
 */

(function() {
    'use strict';

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initScrollAnimations();
        initSmoothScroll();
        initBackToTop();
        initParallaxEffects();
        initCardEnhancements();
        initLoadingTransition();
    });

    /**
     * Scroll-based reveal animations
     */
    function initScrollAnimations() {
        const reveals = document.querySelectorAll('.reveal');
        
        const revealOnScroll = () => {
            const windowHeight = window.innerHeight;
            const revealPoint = 120;

            reveals.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                
                if (elementTop < windowHeight - revealPoint) {
                    element.classList.add('active');
                } else {
                    element.classList.remove('active');
                }
            });
        };

        // Trigger on scroll and initial load
        window.addEventListener('scroll', revealOnScroll, { passive: true });
        window.addEventListener('load', revealOnScroll);
        revealOnScroll(); // Initial call
    }

    /**
     * Smooth scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                if (href === '#' || !href) return;
                
                const targetElement = document.querySelector(href);
                if (!targetElement) return;
                
                e.preventDefault();
                
                const headerOffset = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            });
        });
    }

    /**
     * Back to top button functionality
     */
    function initBackToTop() {
        // Create back to top button if it doesn't exist
        let backToTopBtn = document.querySelector('.back-to-top');
        
        if (!backToTopBtn) {
            backToTopBtn = document.createElement('button');
            backToTopBtn.className = 'back-to-top';
            backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
            backToTopBtn.setAttribute('aria-label', 'Back to top');
            document.body.appendChild(backToTopBtn);
        }

        // Show/hide on scroll
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        }, { passive: true });

        // Scroll to top on click
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Subtle parallax effects for sections
     */
    function initParallaxEffects() {
        const parallaxElements = document.querySelectorAll('.parallax-element');
        
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        }, { passive: true });
    }

    /**
     * Enhanced card hover effects
     */
    function initCardEnhancements() {
        // Card hover effects
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            });
        });

        // Gallery card effects
        document.querySelectorAll('.gallery-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                const img = this.querySelector('img');
                if (img) {
                    img.style.transform = 'scale(1.15)';
                }
            });

            card.addEventListener('mouseleave', function() {
                const img = this.querySelector('img');
                if (img) {
                    img.style.transform = 'scale(1)';
                }
            });
        });

        // Tab item hover effects
        document.querySelectorAll('.tab-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.tab-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.1) rotate(5deg)';
                }
            });

            item.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.tab-icon');
                if (icon) {
                    icon.style.transform = 'scale(1) rotate(0deg)';
                }
            });
        });
    }

    /**
     * Page loading transition
     */
    function initLoadingTransition() {
        document.body.style.opacity = '0';
        
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.body.style.transition = 'opacity 0.6s ease-in';
                document.body.style.opacity = '1';
            }, 100);
        });
    }

    /**
     * Stagger animation for lists and grids
     */
    function staggerAnimation(selector, delay = 100) {
        const elements = document.querySelectorAll(selector);
        
        elements.forEach((element, index) => {
            setTimeout(() => {
                element.classList.add('animate-in');
            }, index * delay);
        });
    }

    /**
     * Intersection Observer for better performance
     */
    if ('IntersectionObserver' in window) {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements with data-animate attribute
        document.querySelectorAll('[data-animate]').forEach(element => {
            observer.observe(element);
        });
    }

    /**
     * Enhanced navigation behavior
     */
    function enhanceNavigation() {
        const nav = document.querySelector('.navbar');
        let lastScroll = 0;
        
        if (!nav) return;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
        }, { passive: true });
    }

    // Initialize navigation enhancements
    enhanceNavigation();

    // Add ripple effect to buttons
    function addRippleEffect() {
        const buttons = document.querySelectorAll('.btn, .tab-item');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });
    }

    addRippleEffect();

    // Log initialization
    console.log('✨ Enhanced animations initialized - AYUSH Department Website');
})();

