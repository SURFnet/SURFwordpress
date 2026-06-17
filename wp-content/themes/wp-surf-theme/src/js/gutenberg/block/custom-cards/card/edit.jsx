import React from 'react'

import {
    _x,
    RichText,
    MediaUpload,
    MediaUploadCheck,
    Button,
} from '@surf/js/gutenberg/packages'

const Edit = ({ attributes, setAttributes }) => {
    const {
        title,
        subtitle,
        imageId,
        imageUrl,
    } = attributes

    const onSelectImage = (media) => {
        setAttributes({
            imageId: media.id,
            imageUrl: media.url
        })
    }

    const removeImage = () => {
        setAttributes({
            imageId: undefined,
            imageUrl: undefined
        })
    }

    return (
        <article className="post-item post-item--block">
            <div className="post-item__inner">
                <div className="post-item__figure">
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={onSelectImage}
                            allowedTypes={['image']}
                            value={imageId}
                            render={({ open }) => (
                                <>
                                    {!imageUrl
                                        ? (
                                            <Button
                                                onClick={open}
                                                className="editor-post-featured-image__toggle"
                                                isSecondary
                                            >
                                                {_x('Upload Image', 'admin', 'wp-surf-theme')}
                                            </Button>
                                        )
                                        : (
                                            <div className="image-container">
                                                <img src={imageUrl} alt={title}/>
                                                <div className="image-controls">
                                                    <Button
                                                        onClick={open}
                                                        isSecondary
                                                    >
                                                        {_x('Replace', 'admin', 'wp-surf-theme')}
                                                    </Button>
                                                    <Button
                                                        onClick={removeImage}
                                                        isDestructive
                                                    >
                                                        {_x('Remove', 'admin', 'wp-surf-theme')}
                                                    </Button>
                                                </div>
                                            </div>
                                        )}
                                </>
                            )}
                        />
                    </MediaUploadCheck>
                </div>
                <div className="post-item__content">
                    <RichText
                        tagName="h3"
                        className="post-item__title"
                        value={title}
                        onChange={title => setAttributes({ title })}
                        placeholder={_x('Add a title...', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        tagName="p"
                        className="post-item__excerpt"
                        value={subtitle}
                        onChange={subtitle => setAttributes({ subtitle })}
                        placeholder={_x('Add a subtitle...', 'admin', 'wp-surf-theme')}
                    />
                </div>
            </div>
        </article>
    )
}

export default Edit
