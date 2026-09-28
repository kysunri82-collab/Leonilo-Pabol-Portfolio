const root = document.documentElement;
root.dataset.theme = localStorage.getItem("theme") || "light";

document.getElementById("theme").onclick = function () {
  root.dataset.theme = root.dataset.theme === "dark" ? "light" : "dark";
  localStorage.setItem("theme", root.dataset.theme);
};

document.querySelector(".menu-btn").onclick = function () {
  document.getElementById("nav").classList.toggle("open");
};

const page = location.pathname.split("/").pop() || "index.html";
document.querySelectorAll("#nav a").forEach(function (link) {
  if (link.getAttribute("href") === page) link.classList.add("active");
});

const topBtn = document.getElementById("top");
window.onscroll = function () {
  topBtn.style.display = window.scrollY > 300 ? "block" : "none";
};
topBtn.onclick = function () {
  window.scrollTo({ top: 0, behavior: "smooth" });
};

const typed = document.getElementById("typed");
if (typed) {
  const words = ["a student", "a web developer", "a creative thinker"];
  let w = 0, c = 0, deleting = false;
  function type() {
    const word = words[w];
    typed.textContent = word.slice(0, c);
    if (!deleting && c < word.length) c++;
    else if (!deleting) { deleting = true; setTimeout(type, 1200); return; }
    else if (c > 0) c--;
    else { deleting = false; w = (w + 1) % words.length; }
    setTimeout(type, deleting ? 50 : 100);
  }
  type();
}

document.querySelectorAll(".tab-btn").forEach(function (btn) {
  btn.onclick = function () {
    document.querySelectorAll(".tab-btn, .tab-panel").forEach(function (el) {
      el.classList.remove("show");
    });
    btn.classList.add("show");
    document.getElementById(btn.dataset.tab).classList.add("show");
  };
});

document.querySelectorAll(".acc-head").forEach(function (head) {
  head.onclick = function () {
    head.parentElement.classList.toggle("open");
  };
});

document.querySelectorAll(".filter").forEach(function (btn) {
  btn.onclick = function () {
    document.querySelectorAll(".filter").forEach(function (b) {
      b.classList.remove("show");
    });
    btn.classList.add("show");
    document.querySelectorAll(".card").forEach(function (card) {
      const match = btn.dataset.f === "all" || card.dataset.type === btn.dataset.f;
      card.style.display = match ? "block" : "none";
    });
  };
});

const form = document.getElementById("form");
if (form) {
  form.onsubmit = function (e) {
    e.preventDefault();
    const name = document.getElementById("fullname").value.trim();
    const email = document.getElementById("email").value.trim();
    const msg = document.getElementById("msg").value.trim();
    const note = document.getElementById("note");
    let error = "";
    if (name.length < 2) error = "Please enter your name.";
    else if (!/^\S+@\S+\.\S+$/.test(email)) error = "Please enter a valid email.";
    else if (msg.length < 10) error = "Message must be at least 10 characters.";
    note.textContent = error || "Thanks, " + name + "! Your message looks good.";
    note.className = error ? "bad" : "good";
    if (!error) form.reset();
  };
}
