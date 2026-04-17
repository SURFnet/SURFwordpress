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
    registerBlockType('surf/asset-examples', {
        title: _x('Placeholder - Asset examples', 'admin', 'wp-surf-theme'),
        description: _x('Placeholder block for inserting the examples at a specific point in the page. So this one can only be added once here.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'list-view'
        },
        category: 'surf',
        postTypes: ['surf-asset'], // Only allow this block on asset pages
        supports: {
            multiple: false, // Only one related assets block per page
            html: false,
            reusable: false
        },
        attributes: {},
        /**
         * Editor render
         */
        edit,
        /**
         * Front-end render
         *
         * @returns {*}
         */
        save: () => null
    })
}
