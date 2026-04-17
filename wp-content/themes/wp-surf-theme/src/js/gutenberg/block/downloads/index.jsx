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
    registerBlockType('surf/downloads', {
        title: _x('Downloads', 'admin', 'wp-surf-theme'),
        description: _x('Shows selected downloads in a grid.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#DC6E02',
            src: 'download'
        },
        category: 'surf',
        attributes: {
            title: {
                type: 'string'
            },
            intro: {
                type: 'string'
            },
            category: {
                type: 'integer'
            },
            buttonText: {
                type: 'string',
                default: __('View all files', 'wp-surf-theme')
            },
            hideImagesOnMobile: {
                type: 'boolean',
                default: false
            }
        },
        example: {
            attributes: {
                title: 'Nullam tincidunt adipiscing enim',
                intro: 'Integer tincidunt. Suspendisse potenti. Sed cursus turpis vitae tortor. Ut varius tincidunt libero. Donec vitae orci sed dolor rutrum auctor.',
                buttonText: __('View all files', 'wp-surf-theme')
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
