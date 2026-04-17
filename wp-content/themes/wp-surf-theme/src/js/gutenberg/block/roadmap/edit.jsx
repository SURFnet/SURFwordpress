import React from 'react'

import {
    _x,
    InnerBlocks,
    RichText,
    InspectorControls,
    PanelBody,
    Panel,
    ToggleControl,
    SelectControl,
    useEffect,
    useSelect,
    useDispatch
} from '@surf/js/gutenberg/packages'
import Theme from '../../../config/theme'

const Edit = ({ attributes, setAttributes, clientId }) => {
    const {
        title,
        subtitle,
        icons,
        display,
        wide,
    } = attributes

    const { updateBlockAttributes } = useDispatch('core/block-editor')

    const childBlocks = useSelect((select) =>
        select('core/block-editor').getBlockOrder(clientId)
    )

    useEffect(() => {
        // Trigger an update to child blocks here if necessary
        // For instance, updating a custom attribute to force re-render
        childBlocks.forEach((childBlockId) => {
            updateBlockAttributes(childBlockId, { lastUpdated: Date.now() })
        })
    }, [childBlocks, updateBlockAttributes])

    return (
        <>
            <InspectorControls>
                <Panel>
                    <PanelBody
                        icon="admin-plugins"
                    >
                        <SelectControl
                            label={_x('Display type', 'admin', 'wp-surf-theme')}
                            onChange={(display) => setAttributes({ display })}
                            value={display}
                            options={[
                                {
                                    label: _x('Flow', 'admin', 'wp-surf-theme'),
                                    value: 'flow'
                                },
                                {
                                    label: _x('Slider', 'admin', 'wp-surf-theme'),
                                    value: 'slider'
                                }
                            ]}
                        />
                        <ToggleControl
                            label={_x('Wide', 'admin', 'wp-surf-theme')}
                            onChange={(wide) => setAttributes({ wide })}
                            checked={wide}
                        />
                        <ToggleControl
                            label={_x('Use Icons', 'admin', 'wp-surf-theme')}
                            onChange={(icons) => setAttributes({ icons })}
                            checked={icons}
                        />
                    </PanelBody>
                </Panel>
            </InspectorControls>
            <div className={
                'surf-block surf-block-roadmap ' +
                (icons ? 'surf-block-roadmap--icons ' : '') +
                (display === 'slider' ? 'surf-block-roadmap--slider ' : '') +
                (display === 'flow' ? 'surf-block-roadmap--flow ' : '')}>
                <div className="surf-block-roadmap__header">
                    <RichText
                        tagName="h2"
                        className="surf-block-roadmap__title"
                        value={title}
                        onChange={title => setAttributes({ title })}
                        placeholder={_x('Add a title...', 'admin', 'wp-surf-theme')}
                    />
                    <RichText
                        tagName="p"
                        className="surf-block-roadmap__subtitle"
                        value={subtitle}
                        onChange={subtitle => setAttributes({ subtitle })}
                        placeholder={_x('Add a subtitle...', 'admin', 'wp-surf-theme')}
                    />
                </div>
                <div className="surf-block-roadmap__items" data-vacancy-wide="true">
                    <InnerBlocks
                        allowedBlocks={['surf/step']}
                        template={
                            [
                                [
                                    'surf/step',
                                    {
                                        backgroundColor: Theme.is(Theme.POWERED_BY_SURF) ? 'white' : 'yellow',
                                        textColor: Theme.is(Theme.POWERED_BY_SURF) ? 'primary' : 'black'
                                    }
                                ]
                            ]
                        }
                    />
                </div>
            </div>
        </>
    )
}

export default Edit
