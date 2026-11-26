/**
 * Language switch in the header: the button opens and closes the list of languages,
 * so the list can also be reached with the keyboard (it still opens on hover as well)
 */
export default function () {
    const openClass = 'is-open'

    const setOpen = (languageSwitch, toggle, open) => {
        languageSwitch.classList.toggle(openClass, open)
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false')
    }

    document.querySelectorAll('[data-language-switch]').forEach((languageSwitch) => {
        const toggle = languageSwitch.querySelector('[data-language-switch-toggle]')
        if (!toggle) {
            return
        }

        toggle.addEventListener('click', () => {
            const isOpen = toggle.getAttribute('aria-expanded') === 'true'
            setOpen(languageSwitch, toggle, !isOpen)
        })

        // Escape closes the list and puts the focus back on the button
        languageSwitch.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape' || toggle.getAttribute('aria-expanded') !== 'true') {
                return
            }

            setOpen(languageSwitch, toggle, false)
            toggle.focus()
        })

        // Close the list when the focus leaves the language switch
        languageSwitch.addEventListener('focusout', (e) => {
            if (languageSwitch.contains(e.relatedTarget)) {
                return
            }

            setOpen(languageSwitch, toggle, false)
        })
    })
}
