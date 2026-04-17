/**
 * WordPress imports
 */
import {
    _x,
    registerBlockType,
} from '@surf/js/gutenberg/packages'

/**
 * Custom imports
 */
import edit from './edit'
import save from './save'
import Theme from '@surf/js/config/theme'

/**
 * Server side block - Register function
 */
export default () => {
    registerBlockType('surf/hero', {
        title: _x('Hero', 'admin', 'wp-surf-theme'),
        description: _x('Shows a header, with various options.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'heading'
        },
        category: 'surf',
        attributes: {
            contentVariation: {
                type: 'string',
                default: 'content-only'
            },
            width: {
                type: 'string',
                default: 'full'
            },
            minHeight: {
                type: 'string',
                default: 'none'
            },
            headingTag: {
                type: 'string',
                default: 'h1'
            },
            title: {
                type: 'string',
                default: ''
            },
            taglineEnabled: {
                type: 'boolean',
                default: false
            },
            tagline: {
                type: 'string',
                default: ''
            },
            buttonEnabled: {
                type: 'boolean',
                default: false
            },
            searchEnabled: {
                type: 'boolean',
                default: false
            },
            searchAlignment: {
                type: 'string',
                default: 'bottom'
            },
            searchWidth: {
                type: 'string',
                default: 'small'
            },
            placeholder: {
                type: 'string',
                default: 'Zoeken...'
            },
            videobackgroundurl: {
                type: 'string',
                default: ''
            },
            backgroundColor: {
                type: 'string',
                default: Theme.is(Theme.POWERED_BY_SURF) ? 'primary' : 'blue'
            },
            textColor: {
                type: 'string',
                default: 'black'
            },
            verticalAlignment: {
                type: 'string',
                default: 'middle'
            },
            horizontalAlignment: {
                type: 'string',
                default: 'center'
            },
            mediaType: {
                type: 'string',
                default: 'image'
            },
            mediaLocation: {
                type: 'string',
                default: 'right'
            },
            image: {
                type: 'object',
                default: null
            },
            video: {
                type: 'string',
                default: ''
            },
            roundedCornersEnabled: { // Add this attribute
                type: 'boolean',
                default: true
            }
        },
        /**
         * Editor render
         */
        edit,
        /**
         * Front-end render
         *
         * @returns {*}
         */
        save,
    })
}
