/**
 * WP Gallery
 */
import { Luminous, LuminousGallery } from 'luminous-lightbox'

export default class WpGallery {
    constructor () {
        this.setProperties()
        this.init()
    }

    init () {
        this.setListeners()
    }

    setProperties () {
        this.galleryLinks = Array.from(document.querySelectorAll('.wp-block-gallery a')).filter(link => link.href.includes('wp-content/uploads'))
        this.imageLinks = Array.from(document.querySelectorAll('.wp-block-image a')).filter(link => link.href.includes('wp-content/uploads'))
    }

    setListeners () {
        new LuminousGallery(this.galleryLinks, {
            arrowNavigation: true,
            caption: true,
            injectBaseStyles: false
        })

        this.imageLinks.forEach(image => {
            if (!image.parentElement.parentElement.classList.contains('wp-block-gallery')) {
                new Luminous(image, {
                    injectBaseStyles: false
                })
            }
        })
    }
}
