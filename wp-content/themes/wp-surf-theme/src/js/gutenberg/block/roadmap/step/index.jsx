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
    registerBlockType('surf/step', {
        title: _x('Step', 'admin', 'wp-surf-theme'),
        description: _x('Steps for the roadmap', 'admin', 'wp-surf-theme'),
        parent: ['surf/roadmap'],
        icon: {
            foreground: '#dc6e02',
            src: 'flag'
        },
        category: 'surf',
        usesContext: [
            'surf/roadmap/display',
            'surf/roadmap/icons'
        ],
        attributes: {
            title: {
                type: 'string'
            },
            subtitle: {
                type: 'string'
            },
            icon: {
                type: 'string',
                default: 'file'
            },
            order: {
                type: 'number',
                default: 1
            }
        },
        example: {
            attributes: {
                title: 'Nam ipsum risus rutrum',
                subtitle: 'Phasellus blandit leo ut',
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
