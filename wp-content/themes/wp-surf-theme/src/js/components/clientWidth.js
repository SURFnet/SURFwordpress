/**
 * Set a custom property with viewport width for use in full screen
 * blocks inside containers. Value is updated on resize.
 * Call init method to initialize.
 */

export default (function () {
    const setProp = function (width) {
        document.documentElement.style.setProperty('--viewport-width', width + 'px')
    }

    const api = {}
    let resizeTimer
    api.init = function () {
        setProp(document.body.clientWidth)

        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer)
            resizeTimer = setTimeout(function () {
                setProp(document.body.clientWidth)
            }, 250)
        })
    }

    return api
})()
