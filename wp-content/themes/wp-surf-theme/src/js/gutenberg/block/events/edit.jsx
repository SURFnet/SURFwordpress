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
            'surf-agenda-category'
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
                </PanelBody>
                <PanelBody title={_x('Display Options', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <ToggleControl
                        label={_x('Hide images on mobile', 'admin', 'wp-surf-theme')}
                        checked={!!hideImagesOnMobile}
                        onChange={(hideImagesOnMobile) => setAttributes({ hideImagesOnMobile })}
                        help={hideImagesOnMobile ? _x('Images will be hidden on mobile devices.', 'admin', 'wp-surf-theme') : _x('Images will be shown on all devices.', 'admin', 'wp-surf-theme')}
                    />
                </PanelBody>
            </InspectorControls>
            <div className="surf-block surf-block-events has-outer-block">
                <div className="block-header container padded">
                    <RichText
                        className="surf-block-events__title block-header__title"
                        tagName="h3"
                        onChange={title => setAttributes({ title })}
                        value={title}
                        placeholder={_x('Title', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        className="surf-block-events__intro block-header__intro"
                        tagName="p"
                        onChange={intro => setAttributes({ intro })}
                        value={intro}
                        placeholder={_x('Intro', 'admin', 'wp-surf-theme')}
                    />
                </div>

                <ServerSideRender
                    block="surf/events"
                    attributes={removeEmptyValues({ category, hideImagesOnMobile })}
                />

                <div className="surf-block-events__bottom block-footer">
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
