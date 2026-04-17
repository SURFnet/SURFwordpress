import React from 'react'
import slugify from 'slugify'

import {
    InnerBlocks
} from '@surf/js/gutenberg/packages'

const Save = ({ attributes }) => {
    const { title } = attributes

    const safeTitle = typeof title === 'string' ? title : 'accordion-item'
    const getSlug = () => slugify(safeTitle, { lower: true })

    return (
        <div className="surf-block surf-accordion faq-item" data-accordion-item>
            <div className="surf-accordion__inner">
                <div id={getSlug()} className="surf-accordion__title faq-item__title h4" data-accordion-button>
                    {title}
                    <svg className="icon icon--arrow-down faq-item__arrow">
                        <use xlinkHref="#global--arrow-down"></use>
                    </svg>
                </div>
                <div className="surf-accordion__content faq-item__content" data-accordion-target>
                    <InnerBlocks.Content/>
                </div>
            </div>
        </div>
    )
}

export default Save
