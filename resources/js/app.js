document.querySelectorAll('[data-booking-price]').forEach((form) => {
    const update = () => {
        const count = form.querySelectorAll('input[name="seats[]"]:checked').length;
        form.querySelector('[data-seat-count]').textContent = count;
        form.querySelector('[data-booking-total]').textContent = (count * Number(form.dataset.bookingPrice)).toFixed(2);
        form.querySelector('[data-booking-submit]').disabled = count === 0;
    };
    form.addEventListener('change', update);
    update();
});

const menuToggle = document.querySelector('[data-menu-toggle]');
const menuPanel = document.querySelector('[data-menu-panel]');
if (menuToggle && menuPanel) {
    menuToggle.addEventListener('click', () => {
        const open = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', String(!open));
        menuPanel.classList.toggle('hidden', open);
    });
}

document.querySelectorAll('[data-carousel]').forEach((carousel) => {
    const track = carousel.querySelector('[data-carousel-track]');
    const slides = [...carousel.querySelectorAll('[data-carousel-slide]')];
    const dots = carousel.querySelector('[data-carousel-dots]');
    if (!track || slides.length < 2 || !dots) return;
    let index = 0;
    const goTo = (next) => {
        index = (next + slides.length) % slides.length;
        track.style.transform = `translateX(-${index * 100}%)`;
        dots.querySelectorAll('.carousel-dot').forEach((dot, i) => dot.classList.toggle('active', i === index));
    };
    slides.forEach((_, i) => { const dot = document.createElement('button'); dot.className = `carousel-dot${i === 0 ? ' active' : ''}`; dot.type = 'button'; dot.setAttribute('aria-label', `Show featured movie ${i + 1}`); dot.addEventListener('click', () => goTo(i)); dots.appendChild(dot); });
    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => goTo(index - 1));
    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => goTo(index + 1));
    let timer = setInterval(() => goTo(index + 1), 6000);
    carousel.addEventListener('mouseenter', () => clearInterval(timer));
    carousel.addEventListener('mouseleave', () => { timer = setInterval(() => goTo(index + 1), 6000); });
});
