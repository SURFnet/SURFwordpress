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
    registerBlockType('surf/card', {
        title: _x('Card', 'admin', 'wp-surf-theme'),
        description: _x('Card for the custom cards block', 'admin', 'wp-surf-theme'),
        parent: ['surf/custom-cards'],
        icon: {
            foreground: '#dc6e02',
            src: 'grid-view'
        },
        category: 'surf',
        usesContext: [
            'surf/custom-cards/display'
        ],
        attributes: {
            title: {
                type: 'string'
            },
            subtitle: {
                type: 'string'
            },
            imageId: {
                type: 'number'
            },
            imageUrl: {
                type: 'string'
            },
            icon: {
                type: 'string',
                default: 'file'
            },
        },
        example: {
            attributes: {
                title: 'Card Title',
                subtitle: 'Card Subtitle',
            },
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
