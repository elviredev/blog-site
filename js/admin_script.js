const menuBtn = document.querySelector('#menu-btn');
const header = document.querySelector('.header');

// Icon Menu hamburger
menuBtn.addEventListener('click', () => {
    header.classList.toggle('active');
})

window.onscroll = () => {
    header.classList.remove('active');
}