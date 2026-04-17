export default function () {
    const checkboxes = document.querySelectorAll('input')

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('click', function () {
            const id = this.id
            const checked = this.checked

            const sameCheckboxes = document.querySelectorAll(`input[id="${id}"]`)
            sameCheckboxes.forEach(checkbox => {
                checkbox.checked = checked
            })
        })
    })
}
