import React from 'react'

import {
    _x,
    RichText,
    InspectorControls,
    PanelBody,
    Panel,
    SelectControl,
    select,
    useSelect
} from '@surf/js/gutenberg/packages'
import Icon from '@surf/js/react/components/Icon'

const Edit = ({ attributes, setAttributes, context, clientId }) => {
    const {
        title,
        subtitle,
        icon,
    } = attributes

    const parentClientId = useSelect((select) => {
        // Get an array of parent client IDs for the current block
        const parents = select('core/block-editor').getBlockParents(clientId)

        // return last element in array
        return parents.at(-1)
    }, [clientId])

    const getBlockOrder = () => {
        // Use the select function to access the block editor's store and retrieve the order of child blocks
        const childBlocksOrder = select('core/block-editor').getBlockOrder(parentClientId)

        // Find the index of the child block's client ID in the array
        const childBlockOrder = childBlocksOrder.indexOf(clientId)

        // The index represents the child block's order (0-based index)
        const order = childBlockOrder + 1

        setAttributes({ order })

        return order
    }

    // context['surf/roadmap/display'] contains the display context. Either 'flow' or 'slider'.
    // context['surf/roadmap/icons'] boolean to show icons or not.

    return (
        <>
            <InspectorControls>
                <Panel>
                    <PanelBody>
                        {context['surf/roadmap/icons'] && (
                            <SelectControl
                                label={_x('Icon', 'admin', 'wp-surf-theme')}
                                onChange={(label) => {
                                    const selectedIcon = window.customData?.roadmapIcons?.find(icon => icon.label === label)
                                    if (selectedIcon) {
                                        setAttributes({ icon: selectedIcon.slug })
                                    }
                                }}
                                value={window.customData?.roadmapIcons?.find(icon => icon.slug === icon)?.label}
                                options={window.customData?.roadmapIcons?.map(icon => ({
                                    label: icon.label,
                                    value: icon.label
                                })) ?? []}
                            />
                        )}
                    </PanelBody>
                </Panel>
            </InspectorControls>
            <div className="surf-block surf-block-step" data-roadmap-display={context['surf/roadmap/display']}>

                {context['surf/roadmap/icons']
                    ? (
                        <div className="surf-block-step__icon">
                            <Icon icon={icon} sprite='#global' className={`icon icon--${icon}`}/>
                        </div>
                    )
                    : (
                        <div className="surf-block-step__number">
                            {getBlockOrder()}
                        </div>
                    )
                }
                <div className="surf-block-step__content">
                    <div className="surf-block-step__text">
                        <RichText
                            tagName="h2"
                            className="surf-block-step__title"
                            value={title}
                            onChange={title => setAttributes({ title })}
                            placeholder={_x('Add a title...', 'admin', 'wp-surf-theme')}
                        />
                        <RichText
                            tagName="p"
                            className="surf-block-step__subtitle"
                            value={subtitle}
                            onChange={subtitle => setAttributes({ subtitle })}
                            placeholder={_x('Add a subtitle...', 'admin', 'wp-surf-theme')}
                        />
                    </div>
                </div>
            </div>
        </>
    )
}

export default Edit
