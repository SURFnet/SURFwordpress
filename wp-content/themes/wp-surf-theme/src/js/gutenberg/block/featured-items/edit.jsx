import React from 'react'

import {
    _x,
    RichText,
    ServerSideRender,
    InspectorControls,
    PanelBody,
    InnerBlocks,
    SelectControl,
    ToggleControl
} from '@surf/js/gutenberg/packages'
import { removeEmptyValues } from '@surf/js/util/helpers'

import PostPickerRepeater from '@surf/js/gutenberg/component/PostPickerRepeater'
import { useSelect } from '../../packages'

const POST_TYPES = ['post', 'page', 'surf-agenda', 'surf-vacancy', 'surf-download', 'surf-asset']
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

const Edit = ({ attributes, setAttributes }) => {
    const { title, intro, layout, posts, hideCategories, hideDates, hideImagesOnMobile } = attributes

    const selectedPosts = useSelect(select => {
        const { getEntityRecord } = select('core')

        const result = Array(posts.length)

        POST_TYPES.forEach((type) => {
            posts.forEach((id, index) => {
                const post = getEntityRecord('postType', type, id)
                if (post) result[index] = post
            })
        })

        return result
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
                        label={_x('Hide categories', 'admin', 'wp-surf-theme')}
                        checked={hideCategories}
                        onChange={hideCategories => setAttributes({ hideCategories })}
                    />
                    <ToggleControl
                        label={_x('Hide dates', 'admin', 'wp-surf-theme')}
                        checked={hideDates}
                        onChange={hideDates => setAttributes({ hideDates })}
                    />
                    <ToggleControl
                        label={_x('Hide images on mobile', 'admin', 'wp-surf-theme')}
                        checked={hideImagesOnMobile}
                        onChange={hideImagesOnMobile => setAttributes({ hideImagesOnMobile })}
                    />
                </PanelBody>
                <PanelBody title={_x('Posts', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <PostPickerRepeater
                        onChange={p => setAttributes({ posts: p.map(p => p.id) })}
                        posts={selectedPosts}
                        postTypes={POST_TYPES}
                    />
                </PanelBody>
            </InspectorControls>
            <div className="surf-block surf-block-featured-items">
                <div className="block-header container padded">
                    <RichText
                        className="surf-block-featured-items__title block-header__title"
                        tagName="h3"
                        onChange={title => setAttributes({ title })}
                        value={title}
                        placeholder={_x('Title', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        className="surf-block-featured-items__intro block-header__intro"
                        tagName="p"
                        onChange={intro => setAttributes({ intro })}
                        value={intro}
                        placeholder={_x('Intro', 'admin', 'wp-surf-theme')}
                    />
                </div>

                <ServerSideRender
                    block="surf/featured-items"
                    attributes={removeEmptyValues({
                        layout,
                        posts,
                        hideCategories,
                        hideDates,
                        hideImagesOnMobile
                    })}
                />

                <div className="surf-block-featured-items__bottom block-footer">
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
