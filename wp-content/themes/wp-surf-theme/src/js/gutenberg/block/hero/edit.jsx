import React from 'react'
import {
    _x,
    BlockControls,
    InspectorControls,
    PanelBody,
    RichText,
    useSelect,
    ToggleControl,
    SelectControl,
    ColorPalette,
    BaseControl,
    MediaReplaceFlow,
    MediaPlaceholder,
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,
    InnerBlocks, TextControl,
} from '@surf/js/gutenberg/packages'

import Theme from '@surf/js/config/theme'

const Edit = ({ clientId, attributes, setAttributes }) => {
    const {
        contentVariation,
        width,
        minHeight,
        headingTag,
        title,
        taglineEnabled,
        tagline,
        buttonEnabled,
        searchEnabled,
        searchAlignment,
        searchWidth,
        placeholder,
        backgroundColor,
        textColor,
        verticalAlignment,
        horizontalAlignment,
        mediaType,
        mediaLocation,
        image,
        video,
        roundedCornersEnabled // Add this attribute
    } = attributes

    const blockIndex = useSelect((select) => {
        const { getBlockIndex } = select('core/block-editor')
        return getBlockIndex(clientId)
    }, [clientId])

    // Get the color palette from the theme settings
    const { colors } = useSelect((select) => {
        const settings = select('core/block-editor').getSettings()
        return {
            colors: window.surfThemeColors?.colorPalette || settings.colors || []
        }
    }, [])
    const defaultHeadingTag = blockIndex === 0 ? 'h1' : 'h2'
    const currentHeadingTag = headingTag || defaultHeadingTag

    if (!headingTag) {
        setAttributes({ headingTag: currentHeadingTag })
    }

    return (
        <>
            <BlockControls>
            </BlockControls>
            <InspectorControls>
                <PanelBody title={_x('Content layer', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <SelectControl
                        label={_x('Content variation', 'admin', 'wp-surf-theme')}
                        value={contentVariation}
                        onChange={(newValue) => setAttributes({ contentVariation: newValue })}
                        options={[
                            { value: 'content-only', label: _x('Content only', 'admin', 'wp-surf-theme') },
                            { value: 'content-with-media', label: _x('Content with media', 'admin', 'wp-surf-theme') },
                            {
                                value: 'content-with-media-background',
                                label: _x('Content with media background', 'admin', 'wp-surf-theme')
                            },
                        ]}
                    />
                    <ToggleGroupControl
                        label={_x('width', 'admin', 'wp-surf-theme')}
                        value={width}
                        onChange={(newWidth) => setAttributes({ width: newWidth })}
                    >
                        <ToggleGroupControlOption value="small" label={_x('Small', 'admin', 'wp-surf-theme')}/>
                        <ToggleGroupControlOption value="full" label={_x('Full', 'admin', 'wp-surf-theme')}/>
                    </ToggleGroupControl>
                    <ToggleGroupControl
                        label={_x('Minimal height', 'admin', 'wp-surf-theme')}
                        value={minHeight}
                        onChange={(newMinHeight) => setAttributes({ minHeight: newMinHeight })}
                    >
                        <ToggleGroupControlOption value="none" label={_x('None', 'admin', 'wp-surf-theme')}/>
                        <ToggleGroupControlOption value="low" label={_x('Low', 'admin', 'wp-surf-theme')}/>
                        <ToggleGroupControlOption value="high" label={_x('High', 'admin', 'wp-surf-theme')}/>
                    </ToggleGroupControl>
                    <hr style={{ margin: '16px 0', borderColor: '#ddd' }}/>
                    <SelectControl
                        label={_x('Select heading tag', 'admin', 'wp-surf-theme')}
                        value={currentHeadingTag}
                        onChange={(newHeadingTag) => setAttributes({ headingTag: newHeadingTag })}
                        options={[
                            { value: 'h1', label: _x('H1', 'admin', 'wp-surf-theme') },
                            { value: 'h2', label: _x('H2', 'admin', 'wp-surf-theme') },
                        ]}
                    />
                    <ToggleControl
                        label={_x('Enable tagline', 'admin', 'wp-surf-theme')}
                        checked={taglineEnabled}
                        onChange={(newValue) => setAttributes({ taglineEnabled: newValue })}
                    />
                    <ToggleControl
                        label={_x('Enable button', 'admin', 'wp-surf-theme')}
                        checked={buttonEnabled}
                        onChange={(newValue) => setAttributes({ buttonEnabled: newValue })}
                    />
                    <ToggleControl
                        label={_x('Enable search', 'admin', 'wp-surf-theme')}
                        checked={searchEnabled}
                        onChange={(newValue) => setAttributes({ searchEnabled: newValue })}
                    />

                    <hr style={{ margin: '16px 0', borderColor: '#ddd' }}/>
                    {searchEnabled && (
                        <>
                            <ToggleGroupControl
                                label={_x('Search alignment', 'admin', 'wp-surf-theme')}
                                value={searchAlignment}
                                onChange={(newAlignment) => setAttributes({ searchAlignment: newAlignment })}
                            >
                                <ToggleGroupControlOption value="top" label={_x('Top', 'admin', 'wp-surf-theme')}/>
                                <ToggleGroupControlOption value="bottom"
                                    label={_x('Bottom', 'admin', 'wp-surf-theme')}/>
                            </ToggleGroupControl>
                            <ToggleGroupControl
                                label={_x('Search width', 'admin', 'wp-surf-theme')}
                                value={searchWidth}
                                onChange={(newWidth) => setAttributes({ searchWidth: newWidth })}
                            >
                                <ToggleGroupControlOption value="small" label={_x('Small', 'admin', 'wp-surf-theme')}/>
                                <ToggleGroupControlOption value="fill" label={_x('Fill', 'admin', 'wp-surf-theme')}/>
                            </ToggleGroupControl>
                            <TextControl
                                label={_x('Placeholder', 'admin', 'wp-surf-theme')}
                                type="text"
                                value={placeholder}
                                onChange={placeholder => setAttributes({ placeholder })}
                            />
                        </>
                    )}

                    <ToggleGroupControl
                        label={_x('Horizontal alignment', 'admin', 'wp-surf-theme')}
                        value={horizontalAlignment}
                        onChange={(newAlignment) => setAttributes({ horizontalAlignment: newAlignment })}
                    >
                        <ToggleGroupControlOption value="left" label={_x('Left', 'admin', 'wp-surf-theme')}/>
                        <ToggleGroupControlOption value="center" label={_x('Center', 'admin', 'wp-surf-theme')}/>
                        <ToggleGroupControlOption value="right" label={_x('Right', 'admin', 'wp-surf-theme')}/>
                    </ToggleGroupControl>
                    <ToggleGroupControl
                        label={_x('Vertical alignment', 'admin', 'wp-surf-theme')}
                        value={verticalAlignment}
                        onChange={(newAlignment) => setAttributes({ verticalAlignment: newAlignment })}
                    >
                        <ToggleGroupControlOption value="top" label={_x('Top', 'admin', 'wp-surf-theme')}/>
                        <ToggleGroupControlOption value="middle" label={_x('Middle', 'admin', 'wp-surf-theme')}/>
                        <ToggleGroupControlOption value="bottom" label={_x('Bottom', 'admin', 'wp-surf-theme')}/>
                    </ToggleGroupControl>
                    <hr style={{ margin: '16px 0', borderColor: '#ddd' }}/>
                    {((contentVariation === 'content-with-media') || (contentVariation === 'content-with-media-background')) && (
                        <>
                            <SelectControl
                                label={_x('Select media type', 'admin', 'wp-surf-theme')}
                                value={mediaType}
                                onChange={(newMediaType) => setAttributes({ mediaType: newMediaType })}
                                options={[
                                    { value: 'image', label: _x('Image', 'admin', 'wp-surf-theme') },
                                    { value: 'video', label: _x('Video', 'admin', 'wp-surf-theme') },
                                ]}/>
                            <ToggleControl
                                label={_x('Enable rounded corners', 'admin', 'wp-surf-theme')}
                                checked={roundedCornersEnabled}
                                onChange={(newValue) => setAttributes({ roundedCornersEnabled: newValue })}
                            />
                            {mediaType === 'image' && (
                                <>
                                    <MediaReplaceFlow
                                        mediaId={image?.id}
                                        mediaURL={image?.url}
                                        onSelect={media => setAttributes({
                                            image: {
                                                id: media.id,
                                                sizes: media.sizes,
                                                url: media.url
                                            }
                                        })}
                                        allowedTypes={['image']}
                                        name={image ? _x('Replace', 'admin', 'wp-surf-theme') : _x('Add Media', 'admin', 'wp-surf-theme')}
                                    />
                                    {contentVariation === 'content-with-media' && (
                                        <ToggleGroupControl
                                            label={_x('Media location', 'admin', 'wp-surf-theme')}
                                            value={mediaLocation}
                                            onChange={(newMediaLocation) => setAttributes({ mediaLocation: newMediaLocation })}
                                        >
                                            <ToggleGroupControlOption value="left"
                                                label={_x('Left', 'admin', 'wp-surf-theme')}/>
                                            <ToggleGroupControlOption value="right"
                                                label={_x('Right', 'admin', 'wp-surf-theme')}/>
                                        </ToggleGroupControl>
                                    )}
                                </>

                            )}

                            {mediaType === 'video' && (
                                <TextControl
                                    type="text"
                                    value={video}
                                    onChange={video => setAttributes({ video })}
                                    placeholder={_x('Video URL', 'admin', 'wp-surf-theme')}
                                />
                            )}
                        </>
                    )}

                    <BaseControl label={_x('Text color', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={textColor}
                            onChange={(newTextColor) => setAttributes({ textColor: newTextColor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                    {contentVariation !== 'content-with-media-background' && (
                        <BaseControl label={_x('Background color', 'admin', 'wp-surf-theme')}>
                            <ColorPalette
                                colors={colors}
                                value={backgroundColor}
                                onChange={(newBackgroundColor) => setAttributes({ backgroundColor: newBackgroundColor })}
                                disableCustomColors={Theme.is(Theme.SURF)}
                            />
                        </BaseControl>
                    )}
                </PanelBody>
                <PanelBody title={_x('Backdrop layer', 'admin', 'wp-surf-theme')} initialOpen={true}>
                </PanelBody>
            </InspectorControls>

            <article className='surf-block-hero'>
                <section className={'surf-block-hero__backdrop-layer'}>

                </section>
                <section
                    style={{ backgroundColor, color: textColor }}
                    className={
                        `surf-block-hero__content-layer ${minHeight && `surf-block-hero__content-layer--${minHeight}`} surf-block-hero__content-layer--${contentVariation} ${(contentVariation === 'content-with-media' && mediaLocation === 'left') && 'surf-block-hero__content-layer--content-with-media--media-left'}`
                    }>
                    <div
                        className={`surf-block-hero__content ${'surf-block-hero__content--' + horizontalAlignment} ${'surf-block-hero__content--' + verticalAlignment} ${'surf-block-hero__content--search-' + searchWidth}`}>
                        {searchEnabled && searchAlignment === 'top' && (
                            <form className="search-form">
                                <label>
                                    <span className="screen-reader-text">Zoeken naar:</span>
                                    <input type="search" className="search-field" placeholder={`${placeholder}`}
                                        value="" name="s"
                                        title="Zoeken naar:"/>
                                </label>
                                <button type="submit" className="search-submit">
                                    <span className="sr-only">Zoeken</span>
                                    <svg className="icon icon--search search-form__icon">
                                        <use xlinkHref="#global--search"></use>
                                    </svg>
                                </button>
                            </form>
                        )}
                        <RichText
                            tagName={headingTag}
                            value={title}
                            onChange={title => setAttributes({ title })}
                            placeholder={_x('Title', 'admin', 'wp-surf-theme')}
                            className='h1'
                        />

                        {taglineEnabled && (
                            <RichText
                                tagName='p'
                                value={tagline}
                                onChange={tagline => setAttributes({ tagline })}
                                placeholder={_x('Tagline', 'admin', 'wp-surf-theme')}
                            />
                        )}

                        {buttonEnabled && (
                            <InnerBlocks
                                allowedBlocks={['core/button']}
                                template={[
                                    ['core/button', {
                                        backgroundColor: Theme.is(Theme.POWERED_BY_SURF) ? 'primary' : 'blue',
                                        textColor: 'white'
                                    }]
                                ]}
                                templateLock="all"
                            />
                        )}

                        {searchEnabled && searchAlignment === 'bottom' && (
                            <form className="search-form">
                                <label>
                                    <span className="screen-reader-text">Zoeken naar:</span>
                                    <input type="search" className="search-field" placeholder={`${placeholder}`}
                                        value="" name="s"
                                        title="Zoeken naar:"/>
                                </label>
                                <button type="submit" className="search-submit">
                                    <span className="sr-only">Zoeken</span>
                                    <svg className="icon icon--search search-form__icon">
                                        <use xlinkHref="#global--search"></use>
                                    </svg>
                                </button>
                            </form>
                        )}
                    </div>
                    {['content-with-media', 'content-with-media-background'].includes(contentVariation) && (
                        <figure
                            className={`surf-block-hero__media ${minHeight !== 'none' ? 'surf-block-hero__media--fill' : ''} ${!roundedCornersEnabled ? 'surf-block-hero__media--no-rounded-corners' : ''}`}>
                            {mediaType === 'image' && image && (
                                <img src={image.url} alt={image.alt || 'Hero Image'} loading="lazy"/>
                            )}
                            {mediaType === 'video' && (
                                <>
                                    {video && (
                                        <video controls>
                                            <source src={video} type="video/mp4"/>
                                            Your browser does not support the video tag.
                                        </video>
                                    )}
                                </>
                            )}
                        </figure>
                    )}
                </section>
                {!image && mediaType === 'image' && ['content-with-media', 'content-with-media-background'].includes(contentVariation) && (
                    <MediaPlaceholder
                        onSelect={media => setAttributes({
                            image: {
                                id: media.id,
                                sizes: media.sizes,
                                url: media.url,
                                alt: media.alt || '',
                            }
                        })}
                        allowedTypes={['image']}
                        value={image || undefined}
                    />
                )}
            </article>
        </>
    )
}

export default Edit
