/**
 * WordPress imports
 */

import {
    __,
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
    registerBlockType('surf/articles', {
        title: _x('Articles', 'admin', 'wp-surf-theme'),
        description: _x('Shows articles in a grid, can set to show the latest posts or posts based on a category.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'admin-post'
        },
        category: 'surf',
        attributes: {
            title: {
                type: 'string'
            },
            intro: {
                type: 'string'
            },
            layout: {
                enum: ['simple', 'auto']
            },
            category: {
                type: 'integer'
            },
            buttonText: {
                type: 'string',
                default: __('Read more articles', 'wp-surf-theme')
            },
            count: {
                type: 'integer',
                default: 3
            },
            hideImagesOnMobile: {
                type: 'boolean',
                default: false
            },
            dateDisplay: {
                enum: ['default', 'hidden', 'published', 'modified', 'both']
            }
        },
        example: {
            attributes: {
                title: 'Nullam tincidunt adipiscing enim',
                intro: 'Integer tincidunt. Suspendisse potenti. Sed cursus turpis vitae tortor. Ut varius tincidunt libero. Donec vitae orci sed dolor rutrum auctor.',
                buttonText: __('Read more articles', 'wp-surf-theme'),
                count: 3,
                layout: 'auto',
                dateDisplay: 'default',
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
