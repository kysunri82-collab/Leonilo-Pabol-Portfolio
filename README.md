# Leonilo Pabol — Personal Portfolio

A responsive 6-page portfolio built with HTML, CSS, and vanilla JavaScript.

## Pages
- `index.html` — Home
- `about.html` — About
- `skills.html` — Skills
- `projects.html` — Projects
- `resume.html` — Resume / Experience
- `contact.html` — Contact

## Structure
```
portfolio/
├── index.html
├── about.html
├── skills.html
├── projects.html
├── resume.html
├── contact.html
├── css/
│   └── style.css
├── js/
│   └── script.js
└── images/        <- add your real photos here (see below)
```

## Images to add
Drop these files into the `images/` folder (any name is fine — just update
the `src` in the HTML to match):
- `leonilo.jpg` — your profile photo (Home page)
- `koukl-book-pic.jpg`, `atw.jpg`, `coffee.jpg` — About page fun facts
- `project-portfolio.jpg`, `project-fbad.jpg`, `project-three.jpg`, `project-four.jpg` — Projects page

Until you add real images, each spot shows a placeholder box so nothing
looks broken.

## JavaScript features
1. Mobile navigation (hamburger menu)
2. Contact form validation
3. Dark / light theme toggle (saved with localStorage)
4. Scroll-to-top button
5. Active nav link highlighting
6. Project filtering (Projects page)
7. Project image modal (Projects page)
8. Animated skill bars (Skills page)

## Editing your content
- Update the placeholder text in `about.html`, `resume.html`, and
  `projects.html` with your real education, experience, and projects.
- Update the social links (Facebook/YouTube/Instagram) and the email
  address in `contact.html` and `index.html`.
- Colors and theme live in `css/style.css` under the `:root` and
  `[data-theme="dark"]` CSS variables at the top of the file.

## Deploying with GitHub Pages
1. Create a new repository on GitHub (e.g. `portfolio`).
2. Push this folder's contents to the repository's `main` branch.
3. In the repo, go to **Settings → Pages**.
4. Under "Build and deployment", set **Source** to `Deploy from a branch`,
   branch `main`, folder `/ (root)`, then Save.
5. Your site will be live at `https://<your-username>.github.io/<repo-name>/`
   within a minute or two.
