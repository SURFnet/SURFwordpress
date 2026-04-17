import React from 'react'

import {
    _x,
    RichText,
    ServerSideRender,
    InspectorControls,
    PanelBody,
    InnerBlocks,
    TextControl,
    ToggleControl
} from '@surf/js/gutenberg/packages'

const Edit = ({ attributes, setAttributes }) => {
    const { title, intro, count, hideImagesOnMobile } = attributes

    return (
        <>
            <InspectorControls>
                <PanelBody title={_x('Settings', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <TextControl
                        label={_x('Amount of posts', 'admin', 'wp-surf-theme')}
                        type="number"
                        min={1}
                        value={count}
                        onChange={count => setAttributes({ count })}
                    />
                    <ToggleControl
                        label={_x('Hide images on mobile', 'admin', 'wp-surf-theme')}
                        checked={hideImagesOnMobile}
                        onChange={hideImagesOnMobile => setAttributes({ hideImagesOnMobile })}
                    />
                </PanelBody>
            </InspectorControls>
            <div className="surf-block surf-block-latest-vacancies alignfull">
                <div className="block-header container padded">
                    <RichText
                        className="surf-block-latest-vacancies__title block-header__title"
                        tagName="h3"
                        onChange={title => setAttributes({ title })}
                        value={title}
                        placeholder={_x('Title', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        className="surf-block-latest-vacancies__intro block-header__intro"
                        tagName="p"
                        onChange={intro => setAttributes({ intro })}
                        value={intro}
                        placeholder={_x('Intro', 'admin', 'wp-surf-theme')}
                    />
                </div>

                <ServerSideRender
                    block="surf/latest-vacancies"
                    attributes={{ count, hideImagesOnMobile }}
                />

                <div className="surf-block-latest-vacancies__bottom block-footer">
                    <InnerBlocks
                        allowedBlocks={['core/button']}
                        template={[
                            ['core/button', {}]
                        ]}
                    />
                </div>
            </div>
        </>
    )
}

export default Edit
