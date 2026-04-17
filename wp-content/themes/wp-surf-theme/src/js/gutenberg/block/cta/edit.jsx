import React from 'react'

import {
    _x,
    InnerBlocks,
    RichText
} from '@surf/js/gutenberg/packages'
import Theme from '../../../config/theme'

const Edit = ({ attributes, setAttributes }) => {
    const { title, subtitle } = attributes

    return (
        <>
            <div className="surf-block surf-block-cta">
                <div className="surf-block-cta__content">
                    <RichText
                        tagName="h2"
                        className="surf-block-cta__title"
                        value={title}
                        onChange={title => setAttributes({ title })}
                        placeholder={_x('Add a title...', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        tagName="p"
                        className="surf-block-cta__subtitle"
                        value={subtitle}
                        onChange={subtitle => setAttributes({ subtitle })}
                        placeholder={_x('Add a subtitle...', 'admin', 'wp-surf-theme')}
                    />
                </div>
                <div className="surf-block-cta__button">
                    <InnerBlocks
                        allowedBlocks={['core/button']}
                        template={
                            [['core/button', Theme.is(Theme.POWERED_BY_SURF)
                                ? {
                                    backgroundColor: 'white',
                                    textColor: 'primary'
                                }
                                : {
                                    backgroundColor: 'yellow',
                                    textColor: 'black'
                                }
                            ]]
                        }
                        templateLock="all"
                    />
                </div>
            </div>
        </>
    )
}

export default Edit
