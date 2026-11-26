export default function () {
    const pageLoad = () => {
        if (!window.location.hash) {
            return
        }

        const id = window.location.hash.replace('#', '')
        const accordion = document.querySelector(`[data-accordion-button][id="${id}"]`)

        if (!accordion) {
            return
        }

        toggleAccordion(accordion, true)
    }

    const setHeightOfBody = (body) => {
        if (!body) {
            return
        }

        if (body.style.maxHeight) {
            body.style.maxHeight = null
        } else {
            body.style.maxHeight = body.scrollHeight + 'px'
        }
    }

    const menuButtonExpand = () => {
        const menuItemButtons = document.querySelectorAll('.menu-item-button')

        if (!menuItemButtons) {
            return
        }

        menuItemButtons.forEach(menuItemButton => {
            // Skip Popover API buttons — they use native popover, not accordion max-height
            if (menuItemButton.hasAttribute('popovertarget')) return

            let expandedState = menuItemButton.getAttribute('aria-expanded')
            const submenu = menuItemButton?.parentElement?.querySelector('.sub-menu')

            menuItemButton.addEventListener('click', function () {
                expandedState = expandedState === 'true' ? 'false' : 'true'
                menuItemButton.setAttribute('aria-expanded', expandedState)
                setHeightOfBody(submenu)
            })
        })
    }

    const toggleAccordion = (button, force = null, skipTransition = false) => {
        const item = button.closest('[data-accordion-item]')
        const body = item.querySelector('[data-accordion-target]')
        if (!body) return

        const isExpanded = button.getAttribute('aria-expanded') === 'true'
        if (isExpanded || force === false) {
            button.setAttribute('aria-expanded', 'false')
            body.style.height = '0px'
            item.classList.remove('is-open')
        }

        if (!isExpanded || force === true) {
            const transition = body.style.transition
            if (skipTransition) {
                body.style.transition = 'none'
            }

            button.setAttribute('aria-expanded', 'true')
            body.style.height = body.scrollHeight + 'px'
            item.classList.add('is-open')

            if (skipTransition) {
                // wait 0.25s before adding transition back.
                setTimeout(() => {
                    body.style.transition = transition
                }, 250)
            }
        }
    }

    const handleClick = () => {
        document.addEventListener('click', (e) => {
            if (!e.target.matches('[data-accordion-button], [data-accordion-button] *')) return
            e.preventDefault()

            const button = e.target.matches('[data-accordion-button]')
                ? e.target
                : e.target.closest('[data-accordion-button]')

            toggleAccordion(button)
        })
    }

    /**
     * Makes the toggles usable with a keyboard and a screen reader. Most toggles are a <div>
     * (like the accordion block and the archive filters), so they get the role and focus of a button.
     */
    const enhanceButtons = () => {
        const usedIds = []

        document.querySelectorAll('[data-accordion-button]').forEach((button, index) => {
            // The accordion block uses its title as ID, so two accordions with the same title get the same ID
            if (button.id) {
                let id = button.id
                let counter = 2
                while (usedIds.includes(id)) {
                    id = `${button.id}-${counter}`
                    counter++
                }
                button.id = id
                usedIds.push(id)
            }

            const item = button.closest('[data-accordion-item]')
            const body = item ? item.querySelector('[data-accordion-target]') : null

            if (body && !button.hasAttribute('aria-controls')) {
                if (!body.id) {
                    body.id = `accordion-content-${index + 1}`
                }
                button.setAttribute('aria-controls', body.id)
            }

            if (!button.hasAttribute('aria-expanded')) {
                button.setAttribute('aria-expanded', 'false')
            }

            if (button.tagName === 'BUTTON') {
                return
            }

            // A role on an element with a heading inside would hide that heading from screen readers,
            // so these toggles keep their markup (they need a button inside the heading in the template)
            if (button.querySelector('h1, h2, h3, h4, h5, h6')) {
                return
            }

            button.setAttribute('role', 'button')
            button.setAttribute('tabindex', '0')
        })
    }

    /**
     * Opens and closes a toggle with Enter or Space, like a native button
     */
    const handleKeys = () => {
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter' && e.key !== ' ') return

            const button = e.target.closest('[data-accordion-button]')

            // A native button already turns these keys into a click
            if (!button || button.tagName === 'BUTTON') return

            // Keep the page from scrolling on Space
            e.preventDefault()
            toggleAccordion(button)
        })
    }

    const anchors = () => {
        const buttons = document.querySelectorAll('[data-accordion-button]:not([id=""])')

        buttons.forEach(button => {
            const id = button.id

            if (!id) {
                return
            }

            const anchors = document.querySelectorAll(`[href="#${id}"]`)

            if (!anchors) {
                return
            }

            // Open accordion when clicking on an anchor that links to it.
            anchors.forEach(anchor => {
                anchor.addEventListener('click', () => {
                    toggleAccordion(button, true)
                })
            })
        })
    }

    const init = () => {
        enhanceButtons()
        handleKeys()
        pageLoad()
        menuButtonExpand()
        handleClick()
        anchors()

        // Handle accordions that should be open on page load
        const accordions = document.querySelectorAll('[data-accordion-button][aria-expanded="true"]')
        accordions.forEach(accordion => {
            toggleAccordion(accordion, true, true)
        })
    }

    init()
}
