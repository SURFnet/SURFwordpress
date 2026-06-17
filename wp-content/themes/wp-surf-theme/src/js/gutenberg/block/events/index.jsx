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
    registerBlockType('surf/events', {
        title: _x('Events', 'admin', 'wp-surf-theme'),
        description: _x('Shows selected events in a grid.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'calendar-alt'
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
                default: __('View more events', 'wp-surf-theme')
            },
            hideImagesOnMobile: {
                type: 'boolean',
                default: false
            },
        },
        example: {
            attributes: {
                title: 'Nullam tincidunt adipiscing enim',
                intro: 'Integer tincidunt. Suspendisse potenti. Sed cursus turpis vitae tortor. Ut varius tincidunt libero. Donec vitae orci sed dolor rutrum auctor.',
                buttonText: __('View more events', 'wp-surf-theme')
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
