/**
 * WordPress imports
 */

import {
    __,
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
    registerBlockType('surf/latest-vacancies', {
        title: _x('Latest vacancies', 'admin', 'wp-surf-theme'),
        description: _x('Shows the latest vacancies in a grid', 'admin', 'wp-surf-theme'),
        icon: {
            foreground: '#dc6e02',
            src: 'feedback'
        },
        category: 'surf',
        attributes: {
            title: {
                type: 'string'
            },
            intro: {
                type: 'string'
            },
            count: {
                type: 'number',
                default: 3
            },
            hideImagesOnMobile: {
                type: 'boolean',
                default: false
            }
        },
        example: {
            attributes: {
                title: 'Nullam tincidunt adipiscing enim',
                intro: 'Integer tincidunt. Suspendisse potenti. Sed cursus turpis vitae tortor. Ut varius tincidunt libero. Donec vitae orci sed dolor rutrum auctor.',
                count: 3
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
        save: () => <InnerBlocks.Content/>
    })
}
