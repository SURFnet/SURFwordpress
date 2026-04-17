import { tns } from 'tiny-slider'

export default function () {
    const setSlider = () => {
        const mobileSliders = document.querySelectorAll('[data-mobile-slider]')

        const vacancySliders = document.querySelectorAll('[data-vacancy-slider]')
        // get custom widht data atrribute

        if (mobileSliders.length > 0) {
            mobileSliders.forEach(slider => {
                tns({
                    loop: false,
                    container: slider,
                    controls: false,
                    items: 1,
                    navPosition: 'bottom',
                    edgePadding: 5,
                    gutter: 15,
                    responsive: {
                        640: {
                            items: 2,
                            edgePadding: 0,
                            gutter: 35
                        },
                        960: {
                            items: 3
                        }
                    }
                })
            })
        }

        if (vacancySliders.length > 0) {
            vacancySliders.forEach(slider => {
                const wide = !!slider.dataset.vacancyWide
                console.log(wide)
                tns({
                    container: slider,
                    loop: false,
                    swipeAngle: true,
                    items: 1,
                    mouseDrag: true,
                    controlsContainer: slider.parentElement.querySelector('#controls'),
                    prevButton: slider.parentElement.querySelector('.previous'),
                    nextButton: slider.parentElement.querySelector('.next'),
                    navPosition: 'bottom',
                    responsive: {
                        640: {
                            items: wide ? 1 : 2,
                        },
                        1024: {
                            items: wide ? 2 : 3,
                        }
                    }
                })
            })
        }
    }

    const init = () => {
        setSlider()
    }

    init()
}
