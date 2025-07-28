// Scroll Animation Observer
const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add("visible");
        }
    });
}, observerOptions);

// Initialize animations when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    // Observe all animated elements
    const animatedElements = document.querySelectorAll(
        ".fade-in, .slide-in-left, .slide-in-right"
    );
    animatedElements.forEach((el) => {
        observer.observe(el);
    });

    // Add staggered animation delay to step cards
    const stepCards = document.querySelectorAll(".step-card");
    stepCards.forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.1}s`;
    });

    // Add staggered animation delay to benefit cards
    const benefitCards = document.querySelectorAll(".benefit-card");
    benefitCards.forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.15}s`;
    });

    // Add staggered animation delay to process cards
    const processCards = document.querySelectorAll(".process-card");
    processCards.forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.2}s`;
    });
});

// Header scroll effect
let lastScrollTop = 0;
const header = document.querySelector(".header");

window.addEventListener("scroll", () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    // Add/remove background based on scroll position
    if (scrollTop > 100) {
        header.style.background = "rgba(255, 255, 255, 0.98)";
        header.style.boxShadow = "0 2px 20px rgba(0, 0, 0, 0.1)";
    } else {
        header.style.background = "rgba(255, 255, 255, 0.95)";
        header.style.boxShadow = "none";
    }

    lastScrollTop = scrollTop;
});

// Smooth scrolling for CTA button
const ctaButton = document.querySelector(".btn-cta");

if (ctaButton) {
    // Check if the element exists
    ctaButton.addEventListener("click", (e) => {
        e.preventDefault();
        // It's also good practice to check if '.how-it-works' exists
        const howItWorksSection = document.querySelector(".how-it-works");
        if (howItWorksSection) {
            howItWorksSection.scrollIntoView({
                behavior: "smooth",
            });
        } else {
            console.warn("Element with class 'how-it-works' not found.");
        }
    });
}

// Button click effects
const buttons = document.querySelectorAll(".btn");
buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
        // Create ripple effect
        const ripple = document.createElement("span");
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.height, rect.width);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + "px";
        ripple.style.left = x + "px";
        ripple.style.top = y + "px";
        ripple.classList.add("ripple");

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Add ripple effect styles
const style = document.createElement("style");
style.textContent = `
    .btn {
        position: relative;
        overflow: hidden;
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Parallax effect for hero section
window.addEventListener("scroll", () => {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector(".hero");
    const rate = scrolled * -0.5;

    if (hero) {
        hero.style.transform = `translateY(${rate}px)`;
    }
});

// No longer needed: Register and Login buttons now use real links. Remove alert popups.

// Add loading animation for step cards
const addLoadingSequence = () => {
    const cards = document.querySelectorAll(".step-card");
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.transform = "scale(1.05)";
            setTimeout(() => {
                card.style.transform = "scale(1)";
            }, 200);
        }, index * 100);
    });
};

// Trigger loading sequence when how-it-works section comes into view
const howItWorksSection = document.querySelector(".how-it-works");

// Check if the element exists before trying to observe it
if (howItWorksSection) {
    const sectionObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    // Assuming addLoadingSequence is defined elsewhere in your script
                    setTimeout(addLoadingSequence, 500);
                    sectionObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.3 }
    );

    sectionObserver.observe(howItWorksSection);
}

// Add hover effects for better interactivity
document
    .querySelectorAll(".step-card, .benefit-card, .process-card")
    .forEach((card) => {
        card.addEventListener("mouseenter", function () {
            this.style.transform = "translateY(-8px) scale(1.02)";
        });

        card.addEventListener("mouseleave", function () {
            this.style.transform = "translateY(0) scale(1)";
        });
    });

// Smooth reveal animation for footer
const footer = document.querySelector(".footer");
const footerObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = "1";
                entry.target.style.transform = "translateY(0)";
            }
        });
    },
    { threshold: 0.1 }
);

footer.style.opacity = "0";
footer.style.transform = "translateY(30px)";
footer.style.transition = "all 0.6s ease";
footerObserver.observe(footer);
