export default function () {
    document.querySelectorAll('.current-menu-item').forEach(el => {
        const parent = el.closest('.menu-item-has-children')

        if (!parent) {
            return
        }

        parent.classList.add('surf-current-page-parent')
    })
}
