import React from 'react'
import PropTypes from 'prop-types'

function Icon ({ icon, sprite, ...attrs }) {
    return (
        <svg {...attrs}>
            <use href={`${sprite}--${icon}`}/>
        </svg>
    )
}

Icon.propTypes = {
    icon: PropTypes.string.isRequired,
    sprite: PropTypes.string.isRequired
}

export default Icon
