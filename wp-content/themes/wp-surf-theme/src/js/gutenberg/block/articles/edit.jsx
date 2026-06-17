import React from 'react'

import {
    _x,
    RichText,
    ServerSideRender,
    InspectorControls,
    PanelBody,
    QueryControls,
    useSelect,
    TextControl,
    SelectControl,
    ToggleControl
} from '@surf/js/gutenberg/packages'
import { removeEmptyValues } from '@surf/js/util/helpers'

const LAYOUTS = [
    {
        value: 'simple',
        label: _x('Simple', 'admin', 'wp-surf-theme')
    },
    {
        value: 'auto',
        label: _x('Auto', 'admin', 'wp-surf-theme')
    }
]

const DATE_OPTIONS = [
    {
        value: 'default',
        label: _x('Site default', 'admin', 'wp-surf-theme')
    },
    {
        value: 'hidden',
        label: _x('Hidden', 'admin', 'wp-surf-theme')
    },
    {
        value: 'published',
        label: _x('Published', 'admin', 'wp-surf-theme')
    },
    {
        value: 'modified',
        label: _x('Modified', 'admin', 'wp-surf-theme')
    },
    {
        value: 'both',
        label: _x('Published and modified', 'admin', 'wp-surf-theme')
    }
]

const Edit = ({ attributes, setAttributes }) => {
    const { title, intro, category, buttonText, count, layout, hideImagesOnMobile, dateDisplay } = attributes

    const categories = useSelect(select => {
        const { getEntityRecords } = select('core')

        return getEntityRecords(
            'taxonomy',
            'category',
            // -1 == 100. Retrieving this using pagination doesn't work because for some reason the API
            // returns 0 results after the first page.
            { per_page: -1 }
        )
    })

    return (
        <>
            <InspectorControls>
                <PanelBody title={_x('Settings', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <SelectControl
                        label={_x('Layout', 'admin', 'wp-surf-theme')}
                        value={layout}
                        options={LAYOUTS}
                        onChange={layout => setAttributes({ layout })}
                    />
                    <ToggleControl
                        label={_x('Hide images on mobile', 'admin', 'wp-surf-theme')}
                        checked={hideImagesOnMobile}
                        onChange={hideImagesOnMobile => setAttributes({ hideImagesOnMobile })}
                    />
                </PanelBody>
                <PanelBody title={_x('Filtering', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <QueryControls
                        categoriesList={categories}
                        onCategoryChange={c => {
                            setAttributes({ category: c ? parseInt(c) : undefined })
                        }}
                        selectedCategoryId={category}
                    />
                    <TextControl
                        label={_x('Amount of posts', 'admin', 'wp-surf-theme')}
                        type="number"
                        min={1}
                        value={count}
                        onChange={c => setAttributes({ count: c ? parseInt(c) : undefined })}
                    />
                </PanelBody>
                <PanelBody title={_x('Publication date settings override', 'admin', 'wp-surf-theme')}
                    initialOpen={true}>
                    <SelectControl
                        label={_x('Post date display', 'admin', 'wp-surf-theme')}
                        value={dateDisplay}
                        options={DATE_OPTIONS}
                        onChange={dateDisplay => setAttributes({ dateDisplay })}
                    />
                </PanelBody>
            </InspectorControls>
            <div className="surf-block surf-block-articles">
                <div className="block-header container padded">
                    <RichText
                        className="surf-block-articles__title block-header__title"
                        tagName="h3"
                        onChange={title => setAttributes({ title })}
                        value={title}
                        placeholder={_x('Title', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        className="surf-block-articles__intro block-header__intro"
                        tagName="p"
                        onChange={intro => setAttributes({ intro })}
                        value={intro}
                        placeholder={_x('Intro', 'admin', 'wp-surf-theme')}
                    />
                </div>
                <ServerSideRender
                    block="surf/articles"
                    attributes={removeEmptyValues({ category, count, layout, hideImagesOnMobile, dateDisplay })}
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
