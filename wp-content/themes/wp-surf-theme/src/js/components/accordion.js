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
                console.log('skipTransition', transition)
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
