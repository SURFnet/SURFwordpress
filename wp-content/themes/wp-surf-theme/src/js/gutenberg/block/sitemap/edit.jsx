/**
 * WordPress imports
 */
import {
    _x,
    InspectorControls,
    PanelBody,
    RichText,
    SelectControl,
    ServerSideRender,
    TextControl,
    ToggleControl,
} from '@surf/js/gutenberg/packages'
import SurfPlaceholder from '@surf/js/gutenberg/component/Placeholder'
import Spinner from '@surf/js/admin/components/Spinner'
import SpriteIcon from '@surf/js/gutenberg/icon/spriteIcon'
import { removeEmptyValues } from '@surf/js/util/helpers'

const Edit = ({ attributes, setAttributes }) => {
    const {
        title,
        postType,
        hideEmpty,
        primaryOnly
    } = attributes

    const postTypes = window?.surf?.blocks?.sitemap?.postTypes ?? []
    const hasNoneChosen = !postType || postType === 'none'

    const renderPostTypeSelect = (label = _x('Post type', 'admin', 'wp-surf-theme')) => {
        return (
            <>
                <SelectControl
                    label={label}
                    value={postType}
                    options={[
                        { label: _x('- Select a post type -', 'admin', 'wp-surf-theme'), value: 'none' },
                        ...postTypes
                    ]}
                    onChange={(value) => setAttributes({ postType: value })}
                />
            </>
        )
    }

    return (
        <>
            <InspectorControls>
                <PanelBody
                    title={_x('Sitemap settings', 'admin', 'wp-surf-theme')}
                    initialOpen={true}
                >
                    <TextControl
                        label={_x('Title', 'admin', 'wp-surf-theme')}
                        value={title}
                        onChange={(value) => setAttributes({ title: value })}
                    />

                    {renderPostTypeSelect()}

                    <ToggleControl
                        label={_x('Hide empty categories', 'admin', 'wp-surf-theme')}
                        checked={hideEmpty}
                        onChange={(value) => setAttributes({ hideEmpty: value })}
                        disabled={hasNoneChosen}
                    />

                    <ToggleControl
                        label={_x('Use Yoast primary category only', 'admin', 'wp-surf-theme')}
                        checked={primaryOnly}
                        onChange={(value) => setAttributes({ primaryOnly: value })}
                        disabled={hasNoneChosen}
                    />

                </PanelBody>
            </InspectorControls>
            <div className="surf-block surf-block-sitemap-preview">
                {hasNoneChosen && (
                    <SurfPlaceholder
                        label="Sitemap"
                        instructions={_x('Select a post type to generate the sitemap preview.', 'admin', 'wp-surf-theme')}
                        variant="block"
                    >
                        {renderPostTypeSelect('')}
                    </SurfPlaceholder>
                )}

                {!hasNoneChosen && (
                    <>
                        <h2 className="surf-block-sitemap__title">
                            <SpriteIcon name="list-tree" sprite="global"/>&nbsp;
                            <RichText
                                tagName="span"
                                value={title}
                                onChange={(value) => setAttributes({ title: value })}
                                placeholder={_x('Add title...', 'admin', 'wp-surf-theme')}
                                className="surf-block-sitemap__title-text"
                            />
                        </h2>
                        <div className="pointer-events-none">
                            <ServerSideRender
                                block="surf/sitemap"
                                attributes={removeEmptyValues({ title, postType, hideEmpty, primaryOnly })}
                                LoadingResponsePlaceholder={() => (
                                    <div style={{ padding: '20px', textAlign: 'center' }}>
                                        <Spinner/>
                                    </div>
                                )}
                            />
                        </div>
                    </>
                )}
            </div>
        </>
    )
}

export default Edit
