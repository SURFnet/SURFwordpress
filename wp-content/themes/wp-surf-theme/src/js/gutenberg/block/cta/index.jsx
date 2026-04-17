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
import save from './save'
import Theme from '@surf/js/config/theme'

/**
 * Server side block - Register function
 */
export default () => {
    registerBlockType('surf/cta', {
        title: _x('Call to action', 'admin', 'wp-surf-theme'),
        description: _x('Call to action block with a title, subtitle and a button.', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'button'
        },
        category: 'surf',
        attributes: {
            title: {
                type: 'string'
            },
            subtitle: {
                type: 'string'
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
        example: {
            attributes: {
                title: 'Nam ipsum risus rutrum',
                subtitle: 'Phasellus blandit leo ut'
            },
            innerBlocks: [
                {
                    name: 'core/button',
                    attributes: {
                        text: __('Click here', 'wp-surf-theme')
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
        save
    })
}
