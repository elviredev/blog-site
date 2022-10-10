const navbar = document.querySelector('.header .flex .navbar')
const menuBtn = document.querySelector('#menu-btn')
const profile = document.querySelector('.header .flex .profile')
const userBtn = document.querySelector('#user-btn')
const searchForm = document.querySelector('.header .flex .search-form')
const searchBtn = document.querySelector('#search-btn')
const allContents = document.querySelectorAll('.posts-grid .box-container .box .content')

menuBtn.addEventListener('click', () => {
    navbar.classList.toggle('active')
    profile.classList.remove('active')
    searchForm.classList.remove('active')
})

userBtn.addEventListener('click', () => {
    profile.classList.toggle('active')
    navbar.classList.remove('active')
    searchForm.classList.remove('active')
})

searchBtn.addEventListener('click', () => {
    searchForm.classList.toggle('active')
    navbar.classList.remove('active')
    profile.classList.remove('active')
})

window.addEventListener('scroll', () => {
    navbar.classList.remove('active')
    profile.classList.remove('active')
    searchForm.classList.remove('active')
})

allContents.forEach(content => {
    if(content.innerHTML.length > 150) {
        content.innerHTML = content.innerHTML.slice(0, 150)
    }
})













