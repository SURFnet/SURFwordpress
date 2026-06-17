export default function () {
    // Wire up scroll buttons for each top menu bar
    document.querySelectorAll('.asset-top-menu__bar').forEach(bar => {
        const wrapper = bar.querySelector('.asset-top-menu__scroll-wrapper')
        const gradient = bar.querySelector('.asset-top-menu__gradient')
        const btnPrev = bar.querySelector('.asset-top-menu__nav-btn--prev')
        const btnNext = bar.querySelector('.asset-top-menu__nav-btn--next')

        if (!wrapper) return

        const scrollAmount = 200

        btnPrev?.addEventListener('click', () => {
            wrapper.scrollBy({ left: -scrollAmount, behavior: 'smooth' })
        })

        btnNext?.addEventListener('click', () => {
            wrapper.scrollBy({ left: scrollAmount, behavior: 'smooth' })
        })

        const updateState = () => {
            const atStart = wrapper.scrollLeft <= 0
            const atEnd = wrapper.scrollLeft + wrapper.clientWidth >= wrapper.scrollWidth - 1
            const overflows = wrapper.scrollWidth > wrapper.clientWidth

            if (btnPrev) btnPrev.disabled = atStart
            if (btnNext) btnNext.disabled = atEnd

            if (gradient) {
                gradient.style.display = (!overflows || atEnd) ? 'none' : ''
            }
        }

        // Close open popovers when the scroll container scrolls. The anchor (trigger button)
        // is inside the scroll wrapper; when it scrolls, some browsers don't reliably
        // update anchor-positioned elements. Closing prevents orphaned/detached popovers.
        const closeOpenPopovers = () => {
            bar.querySelectorAll('.menu-item-button[popovertarget]').forEach((btn) => {
                const popover = document.getElementById(btn.getAttribute('popovertarget'))
                if (popover?.matches?.('[popover]') && popover.matches(':popover-open')) {
                    popover.hidePopover()
                }
            })
        }

        wrapper.addEventListener('scroll', () => {
            updateState()
            closeOpenPopovers()
        }, { passive: true })

        const ro = new ResizeObserver(updateState)
        ro.observe(wrapper)

        updateState()
    })

    const HOVER_CLOSE_DELAY = 150

    const positionPopover = (popover) => {
        const btn = document.querySelector(`[popovertarget="${popover.id}"]`)
        const li = btn?.closest('.asset-top-menu__list > .asset-top-menu__item')
        const scrollWrapper = li?.closest('.asset-top-menu__scroll-wrapper')
        if (!btn || !li) return

        const rect = li.getBoundingClientRect()
        const gap = 16
        const popoverWidth = popover.offsetWidth || 215
        const minMargin = 8
        const scrollLeft = scrollWrapper ? scrollWrapper.getBoundingClientRect().left : minMargin

        // Default: anchor from right (popover extends left). Flip to anchor from left
        // (popover extends right) when it would hit the left edge of the scroll container.
        const wouldHitLeft = rect.right - popoverWidth < scrollLeft
        const extendRight = wouldHitLeft

        popover.style.top = `${rect.bottom + gap}px`
        if (extendRight) {
            let left = rect.left
            if (left + popoverWidth > window.innerWidth - minMargin) {
                left = window.innerWidth - popoverWidth - minMargin
            }
            popover.style.left = `${left}px`
            popover.style.right = 'auto'
        } else {
            let right = window.innerWidth - rect.right
            const leftEdge = rect.right - popoverWidth
            if (leftEdge < minMargin) {
                right = window.innerWidth - minMargin - popoverWidth
            }
            popover.style.left = 'auto'
            popover.style.right = `${right}px`
        }
        popover.style.minWidth = `${Math.max(rect.width, 215)}px`
    }

    document.querySelectorAll('.asset-top-menu__bar .asset-top-menu__item').forEach(li => {
        const btn = li.querySelector('.menu-item-button[popovertarget]')
        const popover = btn ? document.getElementById(btn.getAttribute('popovertarget')) : null
        if (!popover?.matches?.('[popover]')) return

        let closeTimeout = null
        const clearCloseTimer = () => {
            if (closeTimeout) {
                clearTimeout(closeTimeout)
                closeTimeout = null
            }
        }
        const scheduleClose = () => {
            clearCloseTimer()
            closeTimeout = setTimeout(() => popover.hidePopover(), HOVER_CLOSE_DELAY)
        }
        const open = () => {
            clearCloseTimer()
            popover.showPopover()
        }

        li.addEventListener('mouseenter', open)
        li.addEventListener('mouseleave', scheduleClose)
        popover.addEventListener('mouseenter', open)
        popover.addEventListener('mouseleave', scheduleClose)

        popover.addEventListener('toggle', (e) => {
            btn?.setAttribute('aria-expanded', e.newState === 'open' ? 'true' : 'false')
            if (e.newState === 'open') {
                requestAnimationFrame(() => requestAnimationFrame(() => positionPopover(popover)))
            }
        })
    })
}
