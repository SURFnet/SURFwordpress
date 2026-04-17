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
    registerBlockType('surf/separator', {
        title: _x('Separator', 'admin', 'wp-surf-theme'),
        description: _x('Shows a border bar that separates different blocks.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'minus'
        },
        category: 'surf',
        attributes: {},
        example: {
            attributes: {},
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
