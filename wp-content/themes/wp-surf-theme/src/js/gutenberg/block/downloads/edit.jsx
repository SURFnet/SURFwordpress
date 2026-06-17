import React from 'react'

import {
    _x,
    RichText,
    ServerSideRender,
    InspectorControls,
    PanelBody,
    QueryControls,
    useSelect,
    ToggleControl
} from '@surf/js/gutenberg/packages'
import { removeEmptyValues } from '../../../util/helpers'

const Edit = ({ attributes, setAttributes }) => {
    const { title, intro, category, buttonText, hideImagesOnMobile } = attributes

    const categories = useSelect(select => {
        const { getEntityRecords } = select('core')

        return getEntityRecords(
            'taxonomy',
            'surf-download-category'
        )
    })

    return (
        <>
            <InspectorControls>
                <PanelBody title={_x('Filtering', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <QueryControls
                        categoriesList={categories}
                        onCategoryChange={c => {
                            setAttributes({ category: c ? parseInt(c) : undefined })
                        }}
                        selectedCategoryId={category}
                    />
                    <ToggleControl
                        label={_x('Hide images on mobile', 'admin', 'wp-surf-theme')}
                        checked={hideImagesOnMobile}
                        onChange={hideImagesOnMobile => setAttributes({ hideImagesOnMobile })}
                    />
                </PanelBody>
            </InspectorControls>
            <div className="surf-block surf-block-downloads has-outer-block">
                <div className="block-header container padded">
                    <RichText
                        className="surf-block-downloads__title block-header__title"
                        tagName="h3"
                        onChange={title => setAttributes({ title })}
                        value={title}
                        placeholder={_x('Title', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        className="surf-block-downloads__intro block-header__intro"
                        tagName="p"
                        onChange={intro => setAttributes({ intro })}
                        value={intro}
                        placeholder={_x('Intro', 'admin', 'wp-surf-theme')}
                    />
                </div>

                <ServerSideRender
                    block="surf/downloads"
                    attributes={removeEmptyValues({ category, hideImagesOnMobile })}
                />

                <div className="surf-block-articles__bottom block-footer">
                    <RichText
                        className="button"
                        identifier="text"
                        withoutInteractiveFormatting
                        onChange={buttonText => setAttributes({ buttonText })}
                        value={buttonText}
                        placeholder={_x('Add text...', 'admin', 'wp-surf-theme')}
                    />
                </div>
            </div>
        </>
    )
}

export default Edit
