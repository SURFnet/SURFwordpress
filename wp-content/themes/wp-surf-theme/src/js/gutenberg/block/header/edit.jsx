import React from 'react'

import {
    _x,
    BlockControls,
    MediaReplaceFlow,
    MediaPlaceholder,
    InspectorControls,
    PanelBody,
    SelectControl,
    RichText,
    TextControl,
    BaseControl,
    ColorPalette
} from '@surf/js/gutenberg/packages'
import { getImageUrl } from './index'
import Theme from '@surf/js/config/theme'

const Edit = ({ attributes, setAttributes }) => {
    const {
        image,
        title,
        intro,
        variation,
        height,
        search,
        placeholder,
        videourl,
        backgroundtype,
        backgroundimage,
        backgroundcolor,
        blockmargin,
        videobackgroundurl
    } = attributes

    const onSelectMedia = (media) => {
        setAttributes({
            image: {
                id: media.id,
                sizes: media.sizes,
                url: media.url
            }
        })
    }

    const onSelectBackgroundMedia = (media) => {
        setAttributes({
            backgroundimage: {
                id: media.id,
                sizes: media.sizes,
                url: media.url
            }
        })
    }

    return (
        <>
            <BlockControls>
                {['small-image', 'background-image'].includes(variation) && (
                    <MediaReplaceFlow
                        mediaId={image?.id}
                        mediaURL={image?.url}
                        onSelect={onSelectMedia}
                        allowedTypes={['image']}
                        name={image ? _x('Replace', 'admin', 'wp-surf-theme') : _x('Add Media', 'admin', 'wp-surf-theme')}
                    />
                )}

                {['image'].includes(backgroundtype) && (
                    <MediaReplaceFlow
                        mediaId={backgroundimage?.id}
                        mediaURL={backgroundimage?.url}
                        onSelect={onSelectBackgroundMedia}
                        allowedTypes={['image']}
                        name={backgroundimage ? _x('Replace background image', 'admin', 'wp-surf-theme') : _x('Add background image', 'admin', 'wp-surf-theme')}
                    />
                )}
            </BlockControls>
            <InspectorControls>
                <PanelBody title={_x('Foreground', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <SelectControl
                        label={_x('Select a variation', 'admin', 'wp-surf-theme')}
                        value={variation}
                        onChange={variation => setAttributes({ variation })}
                        options={[
                            { value: 'text', label: _x('Text', 'admin', 'wp-surf-theme') },
                            { value: 'small-image', label: _x('Small image', 'admin', 'wp-surf-theme') },
                            { value: 'background-image', label: _x('Background image', 'admin', 'wp-surf-theme') },
                            { value: 'background-video', label: _x('Background video', 'admin', 'wp-surf-theme') },
                            {
                                value: 'background-video-gradient',
                                label: _x('Background video with gradient', 'admin', 'wp-surf-theme')
                            }
                        ]}
                    />
                    <SelectControl
                        label={_x('Height', 'admin', 'wp-surf-theme')}
                        value={height}
                        onChange={height => setAttributes({ height })}
                        options={[
                            { value: 'auto', label: _x('min. 230px', 'admin', 'wp-surf-theme') },
                            { value: 'height', label: _x('min. 460px', 'admin', 'wp-surf-theme') },
                        ]}
                    />
                    <TextControl
                        label={_x('Video URL', 'admin', 'wp-surf-theme')}
                        type="text"
                        value={videobackgroundurl}
                        onChange={videobackgroundurl => setAttributes({ videobackgroundurl })}
                    />
                </PanelBody>
                <PanelBody title={_x('Search', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <SelectControl
                        label={_x('Search', 'admin', 'wp-surf-theme')}
                        value={search}
                        onChange={search => setAttributes({ search })}
                        options={[
                            { value: 'no-search', label: _x('No search', 'admin', 'wp-surf-theme') },
                            { value: 'search-top', label: _x('Search at the top', 'admin', 'wp-surf-theme') },
                            { value: 'search-bottom', label: _x('Search at the bottom', 'admin', 'wp-surf-theme') }
                        ]}
                    />
                    <TextControl
                        label={_x('Placeholder', 'admin', 'wp-surf-theme')}
                        type="text"
                        value={placeholder}
                        onChange={placeholder => setAttributes({ placeholder })}
                    />
                </PanelBody>
                <PanelBody title={_x('Background', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <SelectControl
                        label={_x('Background type', 'admin', 'wp-surf-theme')}
                        value={backgroundtype}
                        onChange={backgroundtype => setAttributes({ backgroundtype })}
                        options={[
                            { value: 'none', label: _x('None', 'admin', 'wp-surf-theme') },
                            { value: 'video', label: _x('Video', 'admin', 'wp-surf-theme') },
                            { value: 'image', label: _x('Image', 'admin', 'wp-surf-theme') },
                            { value: 'color', label: _x('Color', 'admin', 'wp-surf-theme') }
                        ]}
                    />
                    <TextControl
                        label={_x('Video Background URL', 'admin', 'wp-surf-theme')}
                        type="text"
                        value={videourl}
                        onChange={videourl => setAttributes({ videourl })}
                    />
                    <BaseControl label={_x('Background color', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={window.surfThemeColors?.colorPalette || []}
                            value={backgroundcolor}
                            onChange={backgroundcolor => setAttributes({ backgroundcolor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                </PanelBody>
                <PanelBody title={_x('Margin', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <SelectControl
                        label={_x('Margin', 'admin', 'wp-surf-theme')}
                        value={blockmargin}
                        onChange={blockmargin => setAttributes({ blockmargin })}
                        options={[
                            { value: '', label: _x('Normal', 'admin', 'wp-surf-theme') },
                            { value: 'none', label: _x('No margin', 'admin', 'wp-surf-theme') },
                        ]}
                    />
                </PanelBody>
            </InspectorControls>
            <div
                className={`surf-block surf-block-header alignwide surf-block-header--${variation} ${(height === 'auto' || height === 'min-height') ? 'surf-block-header--auto-height' : ''}`}>
                <div className={`surf-block-header__content surf-block-header--${search}`}>
                    <RichText
                        tagName="h1"
                        value={title}
                        onChange={title => setAttributes({ title })}
                        placeholder={_x('Title', 'admin', 'wp-surf-theme')}
                        className="surf-block-header__title"
                    />
                    <RichText
                        tagName="p"
                        value={intro}
                        onChange={intro => setAttributes({ intro })}
                        placeholder={_x('Intro', 'admin', 'wp-surf-theme')}
                        className="surf-block-header__text"
                    />
                </div>
                {['small-image', 'background-image'].includes(variation) && (
                    <div className="surf-block-header__image">
                        {image
                            ? (
                                <img src={getImageUrl(image, variation)} alt={title} loading="lazy"/>
                            )
                            : (
                                <MediaPlaceholder
                                    onSelect={onSelectMedia}
                                    allowedTypes={['image']}
                                    value={image || undefined}
                                />
                            )
                        }
                    </div>
                )}
                {['background-video', 'background-video-gradient'].includes(variation) && (
                    <div className="surf-block-header__video">
                    </div>
                )}
                {['text'].includes(variation) && (
                    <div className="separator">
                        <div className="separator__left">
                            <span></span>
                            <span></span>
                        </div>
                        <div className="separator__right">
                        </div>
                    </div>
                )}
            </div>
        </>
    )
}

export default Edit
