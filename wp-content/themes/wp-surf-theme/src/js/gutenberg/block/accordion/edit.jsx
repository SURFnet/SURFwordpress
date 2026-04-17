import React from 'react'

import {
    _x,
    ButtonBlockAppender,
    InnerBlocks,
    InspectorControls,
    PanelBody,
    RichText,
    ToggleControl
} from '@surf/js/gutenberg/packages'

const Edit = ({ attributes, setAttributes, clientId }) => {
    const { title, advancedContent } = attributes

    // Basic vs advanced block sets
    const basicBlocks = ['core/paragraph']

    const advancedBlocks = [
        'core/paragraph',
        'core/list',
        'core/table',
        'core/image',
        'core/heading'
    ]

    return (
        <>
            {/* Sidebar settings */}
            <InspectorControls>
                <PanelBody title={_x('Content settings', 'admin', 'wp-surf-theme')}>
                    <ToggleControl
                        label={_x('Enable advanced content', 'admin', 'wp-surf-theme')}
                        help={
                            advancedContent
                                ? _x('Additional content blocks are enabled: list, table, heading & image.', 'admin', 'wp-surf-theme')
                                : _x('Only a simple paragraph is allowed.', 'admin', 'wp-surf-theme')
                        }
                        checked={advancedContent}
                        onChange={(value) =>
                            setAttributes({ advancedContent: value })
                        }
                    />
                </PanelBody>
            </InspectorControls>

            {/* Block editor UI */}
            <div className="surf-block surf-accordion faq-item">
                <div className="surf-accordion__inner">

                    <RichText
                        tagName="h2"
                        className="surf-accordion__title faq-item__title h4"
                        value={title}
                        onChange={(title) => setAttributes({ title })}
                        placeholder={_x('Add a title...', 'admin', 'wp-surf-theme')}
                    />

                    <div className="surf-accordion__content faq-item__content">
                        <div className="faq-item__innerblocks-wrapper">
                            <InnerBlocks
                                allowedBlocks={advancedContent ? advancedBlocks : basicBlocks}
                                template={[['core/paragraph']]}
                                templateLock={false}
                                renderAppender={
                                    advancedContent
                                        ? () => <ButtonBlockAppender rootClientId={clientId} />
                                        : false
                                }
                            />
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}

export default Edit
