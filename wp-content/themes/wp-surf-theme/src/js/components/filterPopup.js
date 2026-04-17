export default function () {
    const popups = document.querySelectorAll('.archive-filter__popup')
    const popupTriggers = document.querySelectorAll('[data-toggle-popup]')
    const popupCloseTriggers = document.querySelectorAll('.archive-filter__popup-close')

    const addTriggers = () => {
        popupTriggers.forEach((el) => {
            el.addEventListener('click', () => {
                el.nextElementSibling.classList.add('show')
            })
        })

        popupCloseTriggers.forEach((el) => {
            el.addEventListener('click', () => {
                closePopups()
            })
        })

        window.addEventListener('mouseup', (event) => {
            if (event.target !== popups) {
                closePopups()
            }
        })
    }

    const closePopups = () => {
        popups.forEach((el) => {
            el.classList.remove('show')
        })
    }

    const init = () => {
        addTriggers()
    }

    init()
}
