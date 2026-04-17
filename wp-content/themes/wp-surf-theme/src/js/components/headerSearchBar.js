export default function () {
    const toggleClass = 'is-active'
    const animateClass = 'animate'

    const setClass = () => {
        const searchForm = document.querySelector('.header .search-form')
        const searchToggleButton = document.querySelector('.header .search-submit')
        const searchInput = document.querySelector('.header .search-field')

        if (!searchToggleButton) {
            return
        }

        if (searchInput.value !== '') {
            searchForm.classList.add(toggleClass)
        }

        searchToggleButton.addEventListener('click', (e) => {
            if (searchInput.value === '') {
                e.preventDefault()
                searchForm.classList.add(toggleClass, animateClass)
                searchInput.focus()
            }
        })

        searchInput.addEventListener('focusout', () => {
            if (searchInput.value === '') {
                searchForm.classList.remove(toggleClass)
            }
        })
    }

    const init = () => {
        setClass()
    }

    init()
}
