export default () => {
    document.querySelectorAll('.application-form').forEach(element => {
        const form = element.querySelector('form')
        const button = form.querySelector('.vacancy__apply')

        form.addEventListener('submit', () => {
            button.disabled = true
            button.classList.add('loading')
        })
    })
}
