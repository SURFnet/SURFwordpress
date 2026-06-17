import {
    addFilter,
    InspectorControls,
    __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
    __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
    _x
} from '@surf/js/gutenberg/packages'

// Add custom colours to existing colour settings
addFilter(
    'blocks.registerBlockType',
    'surf/button-custom-colors',
    (settings, name) => {
        if (name !== 'core/button') return settings

        // Add custom colour attributes
        settings.attributes = settings.attributes || {}
        settings.attributes.hoverBackgroundColor = { type: 'string', default: '' }
        settings.attributes.hoverTextColor = { type: 'string', default: '' }
        settings.attributes.customHoverBackgroundColor = { type: 'string', default: '' }
        settings.attributes.customHoverTextColor = { type: 'string', default: '' }

        return settings
    }
)

const withCustomColors = (BlockEdit) => {
    const WithCustomColors = (props) => {
        if (props.name !== 'core/button') {
            return <BlockEdit {...props} />
        }

        const { attributes, setAttributes, clientId } = props

        // Get colour gradient settings for native Gutenberg color experience
        const colorGradientSettings = useMultipleOriginColorsAndGradients()

        // Add custom colours to existing styles
        const styles = {
            ...props.blockProps?.style
        }

        if (attributes.hoverBackgroundColor) {
            styles['--hover-background-color'] = attributes.hoverBackgroundColor
        }
        if (attributes.hoverTextColor) {
            styles['--hover-text-color'] = attributes.hoverTextColor
        }
        if (attributes.customHoverBackgroundColor) {
            styles['--hover-background-color'] = attributes.customHoverBackgroundColor
        }
        if (attributes.customHoverTextColor) {
            styles['--hover-text-color'] = attributes.customHoverTextColor
        }

        // Merge with existing blockProps
        const blockProps = {
            ...props.blockProps,
            style: styles
        }

        // Handle colour changes for palette colours
        const onHoverBackgroundColorChange = (color) => {
            setAttributes({
                hoverBackgroundColor: color,
                customHoverBackgroundColor: color
            })
        }

        const onHoverTextColorChange = (color) => {
            setAttributes({
                hoverTextColor: color,
                customHoverTextColor: color
            })
        }

        return (
            <>
                <BlockEdit {...props} blockProps={blockProps} />
                <InspectorControls group="color">
                    <ColorGradientSettingsDropdown
                        panelId={clientId}
                        settings={[
                            {
                                label: _x('Hover Background Color', 'admin', 'wp-surf-theme'),
                                colorValue: attributes.hoverBackgroundColor || attributes.customHoverBackgroundColor,
                                onColorChange: onHoverBackgroundColorChange,
                            },
                            {
                                label: _x('Hover Text Color', 'admin', 'wp-surf-theme'),
                                colorValue: attributes.hoverTextColor || attributes.customHoverTextColor,
                                onColorChange: onHoverTextColorChange,
                            }
                        ]}
                        {...colorGradientSettings}
                    />
                </InspectorControls>
            </>
        )
    }

    WithCustomColors.displayName = 'WithCustomColors'
    return WithCustomColors
}

// Apply custom colours filter
addFilter('editor.BlockEdit', 'wp-surf-theme/custom-colors', withCustomColors)

// Apply custom colours to saved content
function addCustomColorsToSave (extraProps, blockType, attributes) {
    if (blockType.name !== 'core/button') return extraProps

    const style = { ...extraProps.style }

    if (attributes.hoverBackgroundColor) {
        style['--hover-background-color'] = attributes.hoverBackgroundColor
    }
    if (attributes.hoverTextColor) {
        style['--hover-text-color'] = attributes.hoverTextColor
    }
    if (attributes.customHoverBackgroundColor) {
        style['--hover-background-color'] = attributes.customHoverBackgroundColor
    }
    if (attributes.customHoverTextColor) {
        style['--hover-text-color'] = attributes.customHoverTextColor
    }

    return { ...extraProps, style }
}

addFilter(
    'blocks.getSaveContent.extraProps',
    'surf/custom-colors-save',
    addCustomColorsToSave
)
