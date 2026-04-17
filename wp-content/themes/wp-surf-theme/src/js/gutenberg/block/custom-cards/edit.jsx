import React from 'react'

import {
    _x,
    InnerBlocks,
    RichText,
    InspectorControls,
    PanelBody,
    Panel,
    ToggleControl,
    BaseControl,
    ColorPalette,
    useSelect,
} from '@surf/js/gutenberg/packages'

import Theme from '@surf/js/config/theme'

const Edit = ({ attributes, setAttributes }) => {
    const { title, subtitle, backgroundColor, textColor, hideImagesOnMobile } = attributes

    // Get the color palette from the theme settings
    const { colors } = useSelect((select) => {
        const settings = select('core/block-editor').getSettings()
        return {
            colors: window.surfThemeColors?.colorPalette || settings.colors || [],
        }
    }, [])

    return (
        <>
            <InspectorControls>
                <Panel>
                    <PanelBody>
                        <ToggleControl
                            label={_x('Hide images on mobile', 'admin', 'wp-surf-theme')}
                            onChange={(hideImagesOnMobile) => setAttributes({ hideImagesOnMobile })}
                            checked={hideImagesOnMobile}
                        />
                        <BaseControl label={_x('Text color', 'admin', 'wp-surf-theme')}>
                            <ColorPalette
                                colors={colors}
                                value={textColor}
                                onChange={(newTextColor) => setAttributes({ textColor: newTextColor })}
                                disableCustomColors={Theme.is(Theme.SURF)}
                            />
                        </BaseControl>
                        <BaseControl label={_x('Background color', 'admin', 'wp-surf-theme')}>
                            <ColorPalette
                                colors={colors}
                                value={backgroundColor}
                                onChange={(newBackgroundColor) =>
                                    setAttributes({ backgroundColor: newBackgroundColor })
                                }
                                disableCustomColors={Theme.is(Theme.SURF)}
                            />
                        </BaseControl>
                    </PanelBody>
                </Panel>
            </InspectorControls>
            <div
                style={{ backgroundColor, color: textColor }}
                className={
                    'surf-block surf-block-custom-cards has-outer-block ' +
                    (hideImagesOnMobile ? 'surf-block-custom-cards--hide-mobile ' : '')
                }
            >
                <div className="block-header container padded">
                    <RichText
                        tagName="h2"
                        className="surf-block-custom-cards__title"
                        style={{ color: textColor }}
                        value={title}
                        onChange={(title) => setAttributes({ title })}
                        placeholder={_x('Add a title...', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        tagName="p"
                        className="surf-block-custom-cards__subtitle"
                        value={subtitle}
                        onChange={(subtitle) => setAttributes({ subtitle })}
                        placeholder={_x('Add a subtitle...', 'admin', 'wp-surf-theme')}
                    />
                </div>
                <InnerBlocks
                    className="surf-block-custom-cards__items container padded"
                    data-mobile-slider
                    allowedBlocks={['surf/card']}
                    template={[
                        [
                            'surf/card',
                            {
                                backgroundColor: 'white',
                                textColor: 'primary',
                            },
                        ],
                    ]}
                />
            </div>
        </>
    )
}

export default Edit
