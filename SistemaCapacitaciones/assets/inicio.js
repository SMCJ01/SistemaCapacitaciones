/* ============================================================
   SCROLL REVEAL PARA INICIO
============================================================ */
document.addEventListener("DOMContentLoaded", () => {

    const revealElements = document.querySelectorAll(
        "#inicio-page .futuristic-card, \
         #inicio-page .section-title, \
         #inicio-page .news-item, \
         #inicio-page .hero-title, \
         #inicio-page .hero-sub, \
         #inicio-page .cta-btn-gradient"
    );

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.25 });

    revealElements.forEach(el => observer.observe(el));
});


/* ============================================================
   GLIDE REVEAL – SOLO NOTICIAS
============================================================ */
const glideItems = document.querySelectorAll("#inicio-page .news-item");

const glideObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add("visible");
        }
    });
}, { threshold: 0.3 });

glideItems.forEach(item => glideObserver.observe(item));


/* ============================================================
   HERO TEXT — GLOW PULSE DINÁMICO
============================================================ */
const heroText = document.querySelector("#inicio-page .glow-text");

if (heroText) {
    setInterval(() => {
        heroText.style.textShadow = `
            0 0 ${Math.random() * 20 + 10}px #00d0ff,
            0 0 ${Math.random() * 30 + 15}px #7df9ff
        `;
    }, 1800);
}


/* ============================================================
   TARJETAS 3D tilt al mover el mouse
============================================================ */
document.querySelectorAll("#inicio-page .futuristic-card").forEach(card => {
    card.addEventListener("mousemove", (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;

        const rotateX = y / 25;
        const rotateY = -x / 25;

        card.style.transform =
            `translateY(-12px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });

    card.addEventListener("mouseleave", () => {
        card.style.transform = "translateY(0) rotateX(0) rotateY(0)";
    });
});


/* ============================================================
   ORBES DE LUZ FLOTANTES (futurista)
============================================================ */
const createGlowOrb = () => {
    const orb = document.createElement("div");

    orb.className = "glow-orb";
    document.querySelector("#inicio-page").appendChild(orb);

    const size = Math.random() * 40 + 20;
    orb.style.width = `${size}px`;
    orb.style.height = `${size}px`;
    orb.style.left = `${Math.random() * 100}vw`;
    orb.style.animationDuration = `${Math.random() * 12 + 8}s`;

    setTimeout(() => orb.remove(), 15000);
};

setInterval(createGlowOrb, 1500);


/* ============================================================
   DOCK – ANIMACIÓN DE ÍCONOS
============================================================ */
document.querySelectorAll("#inicio-page .dock-item img").forEach(icon => {
    icon.addEventListener("mouseenter", () => {
        icon.style.transition =
            "transform 0.2s ease-out, filter 0.2s";
        icon.style.transform = "scale(1.4)";
        icon.style.filter = "drop-shadow(0 0 20px #bd00ff)";
    });

    icon.addEventListener("mouseleave", () => {
        icon.style.transform = "scale(1)";
        icon.style.filter = "drop-shadow(0 0 10px #009dff)";
    });
});


/* ============================================================
   PRELOADER – DESAPARECE SUAVEMENTE
============================================================ */
window.addEventListener("load", () => {
    const pre = document.querySelector("#inicio-page #preloader");

    setTimeout(() => {
        if (pre) {
            pre.classList.add("fade-out");
        }
    }, 700);
});
