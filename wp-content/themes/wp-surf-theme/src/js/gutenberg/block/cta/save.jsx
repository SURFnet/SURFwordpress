import React from 'react'

import {
    InnerBlocks,
    RichText
} from '@surf/js/gutenberg/packages'

const Save = ({ attributes }) => {
    const { title, subtitle } = attributes

    return (
        <div className="surf-block surf-block-cta">
            <div className="surf-block-cta__content">
                <RichText.Content
                    tagName="h2"
                    className="surf-block-cta__title"
                    value={title}
                />
                <RichText.Content
                    tagName="p"
                    className="surf-block-cta__subtitle"
                    value={subtitle}
                />
            </div>
            <div className="surf-block-cta__button">
                <InnerBlocks.Content/>
            </div>
        </div>
    )
}

export default Save
