import { disableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock'

export default function () {
    const hamburger = document.querySelector('#hamburger')
    const scroller = document.querySelector('.navigation-mobile')
    const header = document.querySelector('.header')

    const setNavigationHeight = () => {
        const headerHeight = header.offsetHeight
        document.documentElement.style.setProperty('--header-height', headerHeight + 'px')
    }

    if (hamburger && scroller) {
        const openClass = 'is-open'

        const isOpen = () => {
            return document.documentElement.classList.contains('menu-is-open')
        }

        const close = () => {
            hamburger.classList.remove(openClass)
            hamburger.setAttribute('aria-expanded', false)
            scroller.setAttribute('aria-expanded', false)
            scroller.setAttribute('aria-disabled', true)
            document.documentElement.classList.remove('menu-is-open')
            clearAllBodyScrollLocks()
        }

        const open = () => {
            hamburger.classList.add(openClass)
            hamburger.setAttribute('aria-expanded', true)
            scroller.setAttribute('aria-expanded', true)
            scroller.setAttribute('aria-disabled', false)
            document.documentElement.classList.add('menu-is-open')
            disableBodyScroll(scroller)
        }

        hamburger.addEventListener('click', () => {
            isOpen() ? close() : open()
        })
    }

    setNavigationHeight()
    window.addEventListener('resize', () => {
        setNavigationHeight()
    })
}
