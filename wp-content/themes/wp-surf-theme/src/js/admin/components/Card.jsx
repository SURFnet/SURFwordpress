import React from 'react'

const Card = ({ title = null, header = null, className = '', children }) => {
    const Header = header

    return (
        <div className={`metabox-holder admin-card ${className}`.trim()}>
            <div className="postbox">
                <div className="postbox-header">
                    {title && !header && (
                        <h2 className="hndle">{title}</h2>
                    )}

                    {header && <Header/>}
                </div>
                <div className="inside">
                    <div className="main">
                        {children}
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Card
