export default function () {
    const roadmaps = document.querySelectorAll('.tns-slider')
    const sidebar = document.querySelector('.vacancy__sidebar')

    if (roadmaps.length > 0 && sidebar) {
        checkColision(roadmaps, sidebar)
        window.addEventListener('resize', () => checkColision(roadmaps, sidebar))
    }
}

function checkColision (roadmaps, sidebar) {
    for (const roadmap of roadmaps) {
        const sidebarRect = sidebar.getBoundingClientRect()
        const roadmapRect = roadmap.getBoundingClientRect()
        const colision = roadmapRect.top < sidebarRect.bottom && roadmapRect.bottom > sidebarRect.top && roadmapRect.left < sidebarRect.right && roadmapRect.right > sidebarRect.left

        if (colision) {
            roadmap.classList.add('sidebar-is-colliding')
        } else {
            roadmap.classList.remove('sidebar-is-colliding')
        }
    }
}
