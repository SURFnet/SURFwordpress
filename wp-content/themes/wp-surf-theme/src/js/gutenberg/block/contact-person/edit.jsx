import React from 'react'

import {
    _x,
    RichText,
    ServerSideRender,
    InspectorControls,
    PanelBody,
} from '@surf/js/gutenberg/packages'
import { removeEmptyValues } from '@surf/js/util/helpers'

import PostPickerRepeater from '@surf/js/gutenberg/component/PostPickerRepeater'
import { useSelect } from '../../packages'

const POST_TYPES = ['surf-contact-person']

const Edit = ({ attributes, setAttributes }) => {
    const { title, intro, layout, posts, hideCategories, hideDates } = attributes

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
                <PanelBody title={_x('Posts', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <PostPickerRepeater
                        onChange={p => setAttributes({ posts: p.map(p => p.id) })}
                        posts={selectedPosts}
                        postTypes={POST_TYPES}
                        max={1}
                    />
                </PanelBody>
            </InspectorControls>
            <div className="surf-block surf-block-contact-person">
                <div className="container padded surf-block-contact-person__inner">
                    <div className="surf-block-contact-person__person">
                        <ServerSideRender
                            block="surf/contact-person"
                            attributes={removeEmptyValues({ layout, posts, hideCategories, hideDates })}
                        />
                    </div>
                    <div className="surf-block-contact-person__content">
                        <RichText
                            className="h4"
                            tagName="h3"
                            onChange={title => setAttributes({ title })}
                            value={title}
                            placeholder={_x('Title', 'admin', 'wp-surf-theme')}
                        />
                        <RichText
                            tagName="p"
                            onChange={intro => setAttributes({ intro })}
                            value={intro}
                            placeholder={_x('Intro', 'admin', 'wp-surf-theme')}
                        />
                    </div>
                </div>
            </div>
        </>
    )
}

export default Edit
