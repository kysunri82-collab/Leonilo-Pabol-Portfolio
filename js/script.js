/* =========================================================
   Leonilo S. Pabol — Personal Portfolio
   script.js — shared JavaScript for all 6 pages

   Features included (so it's easy to point to when explaining
   the project):
     1. Mobile navigation (hamburger toggle)          [required]
     2. Contact form validation                        [required]
     3. Dark / light theme toggle, saved in localStorage
     4. Scroll-to-top button
     5. Active navigation link highlighting
     6. Project filtering (Projects page)
     7. Project image modal (Projects page)
     8. Animated skill bars (Skills page)
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {
  initMobileNav();
  initThemeToggle();
  initScrollTopButton();
  initActiveNavLink();
  initContactForm();
  initProjectFilter();
  initProjectModal();
  initSkillBars();
});

/* ---------------------------------------------------------
   1. MOBILE NAVIGATION
   Toggles the .open class on the nav links list so the CSS
   media query can slide the menu open/closed on small screens.
--------------------------------------------------------- */
function initMobileNav() {
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");
  if (!hamburger || !navLinks) return;

  hamburger.addEventListener("click", function () {
    navLinks.classList.toggle("open");
    const isOpen = navLinks.classList.contains("open");
    hamburger.setAttribute("aria-expanded", isOpen ? "true" : "false");
    hamburger.textContent = isOpen ? "✕" : "☰";
  });

  // close the mobile menu after a link is tapped
  navLinks.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", function () {
      navLinks.classList.remove("open");
      hamburger.textContent = "☰";
    });
  });
}

/* ---------------------------------------------------------
   3. DARK / LIGHT THEME TOGGLE
   Reads/writes a "theme" key in localStorage so the choice
   persists between pages and future visits.
--------------------------------------------------------- */
function initThemeToggle() {
  const toggleBtn = document.querySelector(".theme-toggle");
  const root = document.documentElement;

  const saved = localStorage.getItem("theme");
  if (saved) {
    root.setAttribute("data-theme", saved);
  }
  updateThemeButtonLabel();

  if (!toggleBtn) return;

  toggleBtn.addEventListener("click", function () {
    const current = root.getAttribute("data-theme") === "dark" ? "dark" : "light";
    const next = current === "dark" ? "light" : "dark";
    root.setAttribute("data-theme", next);
    localStorage.setItem("theme", next);
    updateThemeButtonLabel();
  });

  function updateThemeButtonLabel() {
    if (!toggleBtn) return;
    const current = root.getAttribute("data-theme") === "dark" ? "dark" : "light";
    toggleBtn.textContent = current === "dark" ? "☀ Light" : "🌙 Dark";
  }
}

/* ---------------------------------------------------------
   4. SCROLL-TO-TOP BUTTON
   Shows the button once the user scrolls down the page,
   scrolls smoothly back to top when clicked.
--------------------------------------------------------- */
function initScrollTopButton() {
  const btn = document.getElementById("scroll-top-btn");
  if (!btn) return;

  window.addEventListener("scroll", function () {
    if (window.scrollY > 400) {
      btn.classList.add("show");
    } else {
      btn.classList.remove("show");
    }
  });

  btn.addEventListener("click", function () {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

/* ---------------------------------------------------------
   5. ACTIVE NAV LINK HIGHLIGHTING
   Compares each nav link's href to the current page filename
   and adds the "active" class to the matching one.
--------------------------------------------------------- */
function initActiveNavLink() {
  const links = document.querySelectorAll(".nav-links a");
  let currentPage = window.location.pathname.split("/").pop();
  if (currentPage === "") currentPage = "index.html";

  links.forEach(function (link) {
    const linkPage = link.getAttribute("href");
    if (linkPage === currentPage) {
      link.classList.add("active");
    }
  });
}

/* ---------------------------------------------------------
   2. CONTACT FORM VALIDATION
   Validates name, email, and message fields on submit.
   No page reload happens if validation fails; a success
   message is shown if everything passes (no real server here,
   so nothing is actually sent — see contact.html for notes).
--------------------------------------------------------- */
function initContactForm() {
  const form = document.getElementById("contact-form");
  if (!form) return;

  const nameField = document.getElementById("name");
  const emailField = document.getElementById("email");
  const messageField = document.getElementById("message");
  const statusBox = document.getElementById("form-status");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    isValid = validateField(
      nameField,
      nameField.value.trim().length >= 2,
      "Please enter your full name (at least 2 characters)."
    ) && isValid;

    isValid = validateField(
      emailField,
      /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value.trim()),
      "Please enter a valid email address."
    ) && isValid;

    isValid = validateField(
      messageField,
      messageField.value.trim().length >= 10,
      "Message should be at least 10 characters long."
    ) && isValid;

    statusBox.classList.remove("show", "success", "error");

    if (isValid) {
      statusBox.textContent = "Thanks, " + nameField.value.trim() + "! Your message looks good. (Connect this form to a real backend or a service like Formspree to actually send it.)";
      statusBox.classList.add("show", "success");
      form.reset();
    } else {
      statusBox.textContent = "Please fix the highlighted fields above.";
      statusBox.classList.add("show", "error");
    }
  });

  function validateField(field, condition, message) {
    const errorEl = document.getElementById(field.id + "-error");
    if (!condition) {
      if (errorEl) errorEl.textContent = message;
      field.setAttribute("aria-invalid", "true");
      return false;
    } else {
      if (errorEl) errorEl.textContent = "";
      field.removeAttribute("aria-invalid");
      return true;
    }
  }
}

/* ---------------------------------------------------------
   6. PROJECT FILTERING (Projects page)
   Clicking a filter button shows only the project cards whose
   data-category matches, or all cards for "all".
--------------------------------------------------------- */
function initProjectFilter() {
  const buttons = document.querySelectorAll(".filter-btn");
  const cards = document.querySelectorAll(".project-card");
  if (!buttons.length || !cards.length) return;

  buttons.forEach(function (btn) {
    btn.addEventListener("click", function () {
      buttons.forEach(function (b) { b.classList.remove("active"); });
      btn.classList.add("active");

      const filter = btn.getAttribute("data-filter");
      cards.forEach(function (card) {
        const category = card.getAttribute("data-category");
        const shouldShow = filter === "all" || filter === category;
        card.style.display = shouldShow ? "flex" : "none";
      });
    });
  });
}

/* ---------------------------------------------------------
   7. PROJECT IMAGE MODAL (Projects page)
   Clicking a project thumbnail opens a larger view in a modal
   overlay; closing works via the × button, overlay click, or Esc.
--------------------------------------------------------- */
function initProjectModal() {
  const overlay = document.getElementById("modal-overlay");
  const modalImg = document.getElementById("modal-img");
  const modalTitle = document.getElementById("modal-title");
  const modalDesc = document.getElementById("modal-desc");
  const closeBtn = document.querySelector(".modal-close");
  const thumbs = document.querySelectorAll(".project-thumb");
  if (!overlay || !thumbs.length) return;

  thumbs.forEach(function (thumb) {
    thumb.addEventListener("click", function () {
      const card = thumb.closest(".project-card");
      modalTitle.textContent = card.getAttribute("data-title") || "";
      modalDesc.textContent = card.getAttribute("data-full-desc") || "";
      modalImg.textContent = card.getAttribute("data-title") || "Project preview";
      overlay.classList.add("open");
    });
  });

  function closeModal() { overlay.classList.remove("open"); }

  closeBtn && closeBtn.addEventListener("click", closeModal);
  overlay.addEventListener("click", function (e) {
    if (e.target === overlay) closeModal();
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeModal();
  });
}

/* ---------------------------------------------------------
   8. ANIMATED SKILL BARS (Skills page)
   Uses IntersectionObserver so bars animate to their target
   width only once they scroll into view.
--------------------------------------------------------- */
function initSkillBars() {
  const bars = document.querySelectorAll(".skill-bar-fill");
  if (!bars.length) return;

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        const target = entry.target.getAttribute("data-level") || "0";
        entry.target.style.width = target + "%";
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.4 });

  bars.forEach(function (bar) { observer.observe(bar); });
}
