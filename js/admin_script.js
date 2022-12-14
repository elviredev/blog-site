const menuBtn = document.querySelector('#menu-btn');
const header = document.querySelector('.header');
const postContents = document.querySelectorAll('.show-posts .box-container .box .post-content')

// Icon Menu hamburger
menuBtn.addEventListener('click', () => {
    header.classList.toggle('active');
})

window.onscroll = () => {
    header.classList.remove('active');
}

postContents.forEach(content => {
    if(content.innerHTML.length > 100) {
        content.innerHTML = content.innerHTML.slice(0, 100)
    }
})