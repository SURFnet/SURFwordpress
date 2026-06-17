/**
 * WordPress imports
 */

import {
    _x,
    Button,
    Fragment,
    MediaUpload,
    MediaUploadCheck,
    PanelBody
} from '@surf/js/gutenberg/packages'

const MediaControlPanel = ({
    allowedTypes = ['image'], attribute, callback = () => {
    }, preview = false, title = _x('Media Settings', 'admin', 'wp-surf-theme'), value
}) => {
    const buttonText = value ? _x('Change', 'admin', 'wp-surf-theme') : _x('Choose', 'admin', 'wp-surf-theme')
    const mediaStyle = {
        display: 'block',
        height: 'auto',
        marginBottom: '16px',
        width: '100%'
    }

    return (
        <PanelBody title={title}>
            <Fragment>
                {(preview && value && value.type === 'image') && (
                    <img src={value.src} alt={value.alt} style={mediaStyle}/>
                )}
                {(preview && value && value.type === 'video') && (
                    <video src={value.src} controls style={mediaStyle}></video>
                )}
                <MediaUploadCheck>
                    <MediaUpload
                        allowedTypes={allowedTypes}
                        onSelect={value => setMedia(value, attribute, callback)}
                        render={({ open }) => (
                            <>
                                <Button isPrimary onClick={open}>{buttonText}</Button>
                                {value && <Button isSecondary
                                    onClick={() => setMedia(null, attribute, callback)}
                                    style={{ marginLeft: '0.5em' }}>{_x('Remove', 'admin', 'wp-surf-theme')}</Button>}
                            </>
                        )}
                    />
                </MediaUploadCheck>
            </Fragment>
        </PanelBody>
    )
}

const setMedia = (value, attribute, callback = () => {
}) => {
    const setAttributes = callback
    let media

    if (value) {
        const { alt, id, sizes, type } = value

        media = {
            id,
            type
        }

        if (type === 'image') {
            let imageData = sizes.large
            imageData = imageData || value.sizes.full
            media = {
                ...media,
                src: imageData.url,
                width: imageData.width,
                height: imageData.height,
                alt
            }
        }

        if (type === 'video') {
            media = {
                ...media,
                src: value.url,
                width: value.width,
                height: value.height,
                poster: {
                    src: value?.image?.src || null,
                    width: value?.image?.width || null,
                    height: value?.image?.height || null,
                    isUpload: value?.image?.src?.indexOf('/uploads/') > -1
                }
            }
        }
    } else {
        media = value
    }

    setAttributes({
        [attribute]: media
    })
}

export default MediaControlPanel
