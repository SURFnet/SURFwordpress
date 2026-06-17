/**
 * WordPress imports
 */

import {
    _x,
    registerBlockType
} from '@surf/js/gutenberg/packages'

/**
 * Custom imports
 */
import edit from './edit'

/**
 * Server side block - Register function
 */
export default () => {
    registerBlockType('surf/header', {
        title: _x('Header', 'admin', 'wp-surf-theme'),
        description: _x('Shows a header, with 3 types: Only text, a small image or a full image.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'heading'
        },
        category: 'surf',
        attributes: {
            title: {
                type: 'string',
                default: ''
            },
            intro: {
                type: 'string',
                default: ''
            },
            image: {
                type: 'object',
                default: null
            },
            backgroundimage: {
                type: 'object',
                default: null
            },
            variation: {
                enum: ['text', 'small-image', 'background-image', 'background-video', 'background-video-gradient'],
                default: 'text'
            },
            height: {
                enum: ['auto', 'height'],
                default: 'height'
            },
            search: {
                enum: ['no-search', 'search-top', 'search-bottom'],
                default: 'no-search'
            },
            placeholder: {
                type: 'string',
                default: 'Zoeken...'
            },
            backgroundtype: {
                type: 'string',
                default: 'none'
            },
            videourl: {
                type: 'string',
                default: ''
            },
            backgroundcolor: {
                type: 'string',
                default: ''
            },
            blockmargin: {
                type: 'string',
                default: ''
            },
            videobackgroundurl: {
                type: 'string',
                default: ''
            },
        },
        example: {
            attributes: {
                title: 'Nullam tincidunt adipiscing enim',
                intro: 'Integer tincidunt. Suspendisse potenti. Sed cursus turpis vitae tortor. Ut varius tincidunt libero. Donec vitae orci sed dolor rutrum auctor.',
                variation: 'text'
            },
            viewportWidth: 1024
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
        save: () => {
        }
    })
}

export const getImageUrl = (image, variation) => {
    if (variation === 'small-image' && (image.sizes['hero-large'] ?? false)) {
        return image.sizes['hero-small'].url
    }

    if (variation === 'background-image' && (image.sizes['hero-large'] ?? false)) {
        return image.sizes['hero-large'].url
    }

    return image.url
}
