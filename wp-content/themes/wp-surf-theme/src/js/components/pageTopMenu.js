export default function () {
    document.querySelectorAll('.page-top-menu__item').forEach(el => {
        const anchor = el.querySelector('a')

        if (!anchor) {
            return
        }

        const href = anchor.getAttribute('href')

        if (!href) {
            return
        }

        let hrefUrl = ''
        if (href.startsWith('/')) {
            hrefUrl = new URL(href, window.location.origin)
        } else {
            hrefUrl = new URL(href)
        }

        if (hrefUrl.pathname === '/' && window.location.pathname !== '/') {
            return
        }

        if (window.location.pathname.includes(hrefUrl.pathname)) {
            el.classList.toggle('current-menu-item', true)
        }
    })
}
