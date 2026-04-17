import {
    addFilter,
    InspectorControls,
    PanelBody,
    ToggleControl
} from '@surf/js/gutenberg/packages'

const withMobileVisibility = (BlockEdit) => {
    const WithMobileVisibility = (props) => {
        if (props.name !== 'core/image') {
            return <BlockEdit {...props} />
        }

        const { attributes, setAttributes } = props
        const { className = '' } = attributes

        const toggleMobileVisibility = (hideOnMobile) => {
            let newClassName = className
            if (hideOnMobile) {
                newClassName = className.includes('hide-on-mobile')
                    ? className
                    : `${className} hide-on-mobile`.trim()
            } else {
                newClassName = className.replace('hide-on-mobile', '').trim()
            }
            setAttributes({ className: newClassName })
        }

        const isHiddenOnMobile = className.includes('hide-on-mobile')

        // Add data attribute to block wrapper
        const blockProps = {
            ...props.blockProps,
            'data-hide-on-mobile': isHiddenOnMobile ? 'true' : 'false'
        }

        return (
            <>
                <BlockEdit {...props} blockProps={blockProps}/>
                <InspectorControls>
                    <PanelBody title="Settings" initialOpen={true}>
                        <ToggleControl
                            label="Hide on mobile"
                            checked={isHiddenOnMobile}
                            onChange={toggleMobileVisibility}
                        />
                    </PanelBody>
                </InspectorControls>
            </>
        )
    }

    WithMobileVisibility.displayName = 'WithMobileVisibility'
    return WithMobileVisibility
}

addFilter('editor.BlockEdit', 'wp-surf-theme/image-mobile-visibility', withMobileVisibility)
