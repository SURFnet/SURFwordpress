import React from 'react'

import {
    _x,
    InspectorControls,
    PanelBody,
    SelectControl,
    InnerBlocks,
    useSelect,
    ColorPalette,
    BaseControl,
    ColorIndicator
} from '@surf/js/gutenberg/packages'

import Theme from '@surf/js/config/theme'

const Edit = ({ attributes, setAttributes }) => {
    const {
        separator,
        separatorposition,
        bgcolor,
        titlecolor,
        textcolor,
        linkcolor,
        linkcolorhover,
        buttonbgcolor,
        buttontextcolor,
        buttonbgcolorhover,
        buttontextcolorhover,
        blockmargin
    } = attributes

    // Get the color palette from the theme settings
    const { colors } = useSelect((select) => {
        const settings = select('core/block-editor').getSettings()
        return {
            colors: window.surfThemeColors?.colorPalette || settings.colors || []
        }
    }, [])

    return (
        <>
            <InspectorControls>
                <PanelBody title={_x('Divider', 'admin', 'wp-surf-theme')} initialOpen={true}>
                    <SelectControl
                        label={_x('Divider', 'admin', 'wp-surf-theme')}
                        value={separator}
                        onChange={separator => setAttributes({ separator })}
                        options={[
                            { value: 'none', label: _x('None', 'admin', 'wp-surf-theme') },
                            { value: 'standard-separator', label: _x('Standard', 'admin', 'wp-surf-theme') },
                        ]}
                    />
                    <SelectControl
                        label={_x('Position', 'admin', 'wp-surf-theme')}
                        value={separatorposition}
                        onChange={separatorposition => setAttributes({ separatorposition })}
                        options={[
                            { value: 'none', label: _x('Make a selection', 'admin', 'wp-surf-theme') },
                            { value: 'both', label: _x('Both sides', 'admin', 'wp-surf-theme') },
                            { value: 'top', label: _x('Top only', 'admin', 'wp-surf-theme') },
                            { value: 'bottom', label: _x('Bottom only', 'admin', 'wp-surf-theme') }
                        ]}
                    />
                </PanelBody>
                <PanelBody
                    title={
                        <>
                            {_x('Style', 'admin', 'wp-surf-theme')}
                            <div style={{ marginLeft: 'auto', display: 'flex', gap: '3px' }}>
                                {bgcolor && <ColorIndicator colorValue={bgcolor}/>}
                                {titlecolor && <ColorIndicator colorValue={titlecolor}/>}
                                {textcolor && <ColorIndicator colorValue={textcolor}/>}
                                {linkcolor && <ColorIndicator colorValue={linkcolor}/>}
                                {linkcolorhover && <ColorIndicator colorValue={linkcolorhover}/>}
                            </div>
                        </>
                    }
                    initialOpen={true}
                >
                    {/* <TextControl
                        label={_x('Background (HEX)', 'admin', 'wp-surf-theme')}
                        type="text"
                        value={bgcolor}
                        onChange={bgcolor => setAttributes({ bgcolor })}
                    /> */}

                    <BaseControl label={_x('Background', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            asButtons={true}
                            colors={colors}
                            value={bgcolor}
                            onChange={(newBackgroundColor) => setAttributes({ bgcolor: newBackgroundColor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                    <BaseControl label={_x('Title', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={titlecolor}
                            onChange={(newTitleColor) => setAttributes({ titlecolor: newTitleColor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                    <BaseControl label={_x('Text', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={textcolor}
                            onChange={(newTextColor) => setAttributes({ textcolor: newTextColor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                    <BaseControl label={_x('Link', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={linkcolor}
                            onChange={(newLinkColor) => setAttributes({ linkcolor: newLinkColor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                    <BaseControl label={_x('Hover - Link', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={linkcolorhover}
                            onChange={(newLinkColor) => setAttributes({ linkcolorhover: newLinkColor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                </PanelBody>
                <PanelBody
                    title={
                        <>
                            {_x('Buttons', 'admin', 'wp-surf-theme')}
                            <div style={{ marginLeft: 'auto', display: 'flex', gap: '3px' }}>
                                {buttonbgcolor && <ColorIndicator colorValue={buttonbgcolor}/>}
                                {buttontextcolor && <ColorIndicator colorValue={buttontextcolor}/>}
                                {buttonbgcolorhover && <ColorIndicator colorValue={buttonbgcolorhover}/>}
                                {buttontextcolorhover && <ColorIndicator colorValue={buttontextcolorhover}/>}
                            </div>
                        </>
                    }
                    initialOpen={true}
                >
                    <BaseControl label={_x('Background', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={buttonbgcolor}
                            onChange={(newButtonBgColor) => setAttributes({ buttonbgcolor: newButtonBgColor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                    <BaseControl label={_x('Text', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={buttontextcolor}
                            onChange={(newButtonTextColor) => setAttributes({ buttontextcolor: newButtonTextColor })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                    <BaseControl label={_x('Hover - Background', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={buttonbgcolorhover}
                            onChange={(newButtonBgColorHover) => setAttributes({ buttonbgcolorhover: newButtonBgColorHover })}
                            disableCustomColors={Theme.is(Theme.SURF)}
                        />
                    </BaseControl>
                    <BaseControl label={_x('Hover - Text', 'admin', 'wp-surf-theme')}>
                        <ColorPalette
                            colors={colors}
                            value={buttontextcolorhover}
                            onChange={(newButtonTextColorHover) => setAttributes({ buttontextcolorhover: newButtonTextColorHover })}
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
            <div className={'surf-block surf-block-style-group alignfull'}
                style={{
                    color: textcolor,
                    background: bgcolor,
                    '--surf-color-articles-background': bgcolor,
                    '--surf-color-background': bgcolor,
                    '--surf-color-text': textcolor,
                    '--surf-color-link': linkcolor,
                    '--surf-color-headings': titlecolor,
                    '--surf-color-post-meta': titlecolor,
                    '--surf-color-primary:': textcolor,
                    '--surf-color-button': buttonbgcolor,
                    '--surf-color-button-text': buttontextcolor,
                    '--surf-color-button-hover': buttonbgcolorhover,
                    '--surf-color-button-hover-text': buttontextcolorhover
                }}
            >
                {separator === 'standard-separator' && separatorposition === 'both' &&
                    <div
                        className={'surf-block-style-group__separator'}>{_x('Divider', 'admin', 'wp-surf-theme')}</div>}
                {separator === 'standard-separator' && separatorposition === 'top' &&
                    <div
                        className={'surf-block-style-group__separator'}>{_x('Divider', 'admin', 'wp-surf-theme')}</div>}
                <div className={'surf-block-style-group__inner'}>
                    <InnerBlocks/>
                </div>
                {separator === 'standard-separator' && separatorposition === 'both' &&
                    <div
                        className={'surf-block-style-group__separator'}>{_x('Divider', 'admin', 'wp-surf-theme')}</div>}
                {separator === 'standard-separator' && separatorposition === 'bottom' &&
                    <div
                        className={'surf-block-style-group__separator'}>{_x('Divider', 'admin', 'wp-surf-theme')}</div>}
            </div>
        </>
    )
}

export default Edit
