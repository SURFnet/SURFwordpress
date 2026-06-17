/**
 * WordPress imports
 */

import {
    _x,
    InnerBlocks,
    registerBlockType
} from '@surf/js/gutenberg/packages'

/**
 * Custom imports
 */
import edit from './edit'
import Theme from '@surf/js/config/theme'

/**
 * Server side block - Register function
 */

export default () => {
    registerBlockType('surf/roadmap', {
        title: _x('Roadmap', 'admin', 'wp-surf-theme'),
        description: _x('Roadmap block with a title, sub title and steps', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'editor-ol'
        },
        category: 'surf',
        attributes: {
            title: {
                type: 'string'
            },
            subtitle: {
                type: 'string'
            },
            icons: {
                type: 'boolean',
                default: false
            },
            display: {
                type: 'string',
                default: 'flow'
            },
            wide: {
                type: 'boolean',
                default: false,
            },
            backgroundColor: {
                type: 'string',
                default: Theme.is(Theme.POWERED_BY_SURF) ? 'primary' : 'blue'
            },
            textColor: {
                type: 'string',
                default: 'white'
            }
        },
        providesContext: {
            'surf/roadmap/display': 'display',
            'surf/roadmap/icons': 'icons'
        },
        example: {
            attributes: {
                title: 'Nam ipsum risus rutrum',
                subtitle: 'Phasellus blandit leo ut',
            },
            innerBlocks: [
                {
                    name: 'surf/step',
                    attributes: {
                        title: 'Nam ipsum risus rutrum',
                        subtitle: 'Phasellus blandit leo ut',
                    }
                }
            ],
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
        save: () => <InnerBlocks.Content/>
    })
}
