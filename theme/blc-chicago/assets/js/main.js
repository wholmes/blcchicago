(() => {
  const nav = document.getElementById("site-nav");
  const toggle = document.querySelector(".nav-toggle");
  if (nav && toggle) {
    toggle.addEventListener("click", () => {
      const open = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  const revealEls = document.querySelectorAll(".reveal:not([data-directory-list])");
  if (revealEls.length && "IntersectionObserver" in window) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.16, rootMargin: "0px 0px -8% 0px" }
    );
    revealEls.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add("is-visible"));
  }

  const filterForm = document.querySelector("[data-directory-filters]");
  const list = document.querySelector("[data-directory-list]");
  const countEl = document.querySelector("[data-directory-count]");
  const viewToggle = document.querySelector("[data-directory-view-toggle]");
  if (viewToggle && list) {
    viewToggle.addEventListener("click", (event) => {
      const btn = event.target.closest("[data-view]");
      if (!btn) return;
      const view = btn.dataset.view;
      list.classList.toggle("directory-list--cards", view === "cards");
      viewToggle.querySelectorAll("[data-view]").forEach((toggle) => {
        const active = toggle.dataset.view === view;
        toggle.classList.toggle("is-active", active);
        toggle.setAttribute("aria-pressed", active ? "true" : "false");
      });
    });
  }
  if (filterForm && list) {
    list.classList.add("is-visible");
    const rows = Array.from(list.querySelectorAll(".member-row"));
    const apply = () => {
      const q = (filterForm.q?.value || "").trim().toLowerCase();
      const tier = filterForm.tier?.value || "";
      const industry = filterForm.industry?.value || "";
      let visible = 0;
      rows.forEach((row) => {
        const name = (row.dataset.name || "").toLowerCase();
        const rowTier = row.dataset.tier || "";
        const rowIndustry = row.dataset.industry || "";
        const matchQ = !q || name.includes(q);
        const matchTier = !tier || rowTier === tier;
        const matchIndustry = !industry || rowIndustry === industry;
        const show = matchQ && matchTier && matchIndustry;
        row.hidden = !show;
        if (show) visible += 1;
      });
      if (countEl) {
        const noun = countEl.textContent?.includes("member") ? "members" : "organizations";
        countEl.textContent = `${visible} ${noun}`;
      }
    };
    filterForm.addEventListener("input", apply);
    filterForm.addEventListener("change", apply);
  }

  const ticketPanel = document.querySelector("[data-event-tickets]");
  if (ticketPanel) {
    const params = new URLSearchParams(window.location.search);
    const isMember = params.get("member") === "1";
    const guestBlock = ticketPanel.querySelector("[data-tickets-guest]");
    const memberBlock = ticketPanel.querySelector("[data-tickets-member]");
    if (isMember && guestBlock && memberBlock) {
      guestBlock.hidden = true;
      memberBlock.hidden = false;
    }
  }
})();
