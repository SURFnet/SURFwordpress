/**
 * WordPress imports
 */

import {
    _x,
    registerBlockType,
    InnerBlocks
} from '@surf/js/gutenberg/packages'

/**
 * Custom imports
 */
import edit from './edit'

/**
 * Server side block - Register function
 */
export default () => {
    registerBlockType('surf/style-group', {
        title: _x('Style group', 'admin', 'wp-surf-theme'),
        description: _x('Creates a block where you can change your background, text colors, and button colors.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'align-full-width'
        },
        category: 'surf',
        attributes: {
            separator: {
                enum: ['none', 'standard-separator'],
                default: 'none'
            },
            separatorposition: {
                enum: ['none', 'both', 'top', 'bottom'],
                default: 'none'
            },
            bgcolor: {
                type: 'string',
                default: ''
            },
            titlecolor: {
                type: 'string',
                default: ''
            },
            textcolor: {
                type: 'string',
                default: ''
            },
            linkcolor: {
                type: 'string',
                default: ''
            },
            linkcolorhover: {
                type: 'string',
                default: ''
            },
            buttonbgcolor: {
                type: 'string',
                default: ''
            },
            buttontextcolor: {
                type: 'string',
                default: ''
            },
            buttonbgcolorhover: {
                type: 'string',
                default: ''
            },
            buttontextcolorhover: {
                type: 'string',
                default: ''
            },
            blockmargin: {
                type: 'string',
                default: ''
            }
        },
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
            return <InnerBlocks.Content/>
        }
    })
}
