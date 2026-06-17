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
import save from './save'

/**
 * Server side block - Register function
 */
export default () => {
    registerBlockType('surf/accordion', {
        title: _x('Accordion', 'admin', 'wp-surf-theme'),
        description: _x('Add an accordion to the page, add multiple to get a list.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'button'
        },
        category: 'surf',
        attributes: {
            title: {
                type: 'string'
            },
            advancedContent: {
                type: 'boolean',
                default: false
            }
        },
        example: {
            attributes: {
                title: 'Nam ipsum risus rutrum',
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
        save
    })
}
