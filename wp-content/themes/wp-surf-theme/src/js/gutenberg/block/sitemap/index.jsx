/**
 * WordPress imports
 */

import {
    _x,
    __,
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
    registerBlockType('surf/sitemap', {
        title: _x('CPT sitemap', 'admin', 'wp-surf-theme'),
        description: _x('Shows a sitemap (table of contents) for the specified post type.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#DC6E02',
            src: 'editor-ul'
        },
        category: 'surf',
        attributes: {
            title: {
                type: 'string',
                default: __('Content', 'wp-surf-theme'),
            },
            postType: {
                type: 'string',
                default: 'none',
            },
            hideEmpty: {
                type: 'boolean',
                default: true,
            },
            primaryOnly: {
                type: 'boolean',
                default: false,
            },
        },
        example: {
            attributes: {
                title: __('Content', 'wp-surf-theme'),
                postType: 'surf-asset',
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
