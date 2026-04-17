import React from 'react'

// Accepts the same icon names as in your sprite imports
const SpriteIcon = ({ name, sprite = 'global', className = '', ...props }) => {
    if (!name) {
        return null
    }

    const iconClass = ('icon icon--' + name + ' ' + className).trim()
    return <svg aria-hidden="true" className={iconClass} {...props}>
        <use xlinkHref={'#' + sprite + '--' + name}/>
    </svg>
}

export default SpriteIcon
