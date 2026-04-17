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
    registerBlockType('surf/related-assets', {
        title: _x('Placeholder - Related assets', 'admin', 'wp-surf-theme'),
        description: _x('Placeholder block for inserting related assets at a specific point in the page. So this one can only be added once here.', 'admin', 'wp-surf-theme'),
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
