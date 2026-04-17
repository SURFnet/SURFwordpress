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
    registerBlockType('surf/custom-cards', {
        title: _x('Custom Cards', 'admin', 'wp-surf-theme'),
        description: _x('Custom cards block with a title, subtitle and cards', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'columns'
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
            },
            hideImagesOnMobile: {
                type: 'boolean',
                default: false
            }
        },
        example: {
            attributes: {
                title: 'Custom Cards Title',
                subtitle: 'Custom Cards Subtitle',
            },
            innerBlocks: [
                {
                    name: 'surf/card',
                    attributes: {
                        title: 'Card Title',
                        subtitle: 'Card Subtitle',
                        imageId: 123,
                        imageUrl: 'https://via.placeholder.com/150'
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
        save: () => <InnerBlocks.Content/>,
    })
}
