import React from 'react'

const { _x } = wp.i18n

const Footer = ({ onNext, onPrevious, nextDisabled = false, previousDisabled = false }) => {
    return (
        <div className="setup-wizard__footer">
            <button onClick={onPrevious} className="button is-primary" disabled={previousDisabled}>
                {_x('Previous', 'admin', 'wp-surf-theme')}
            </button>
            <button onClick={onNext} className="button is-primary" disabled={nextDisabled}>
                {_x('Next', 'admin', 'wp-surf-theme')}
            </button>
        </div>
    )
}

export default Footer
