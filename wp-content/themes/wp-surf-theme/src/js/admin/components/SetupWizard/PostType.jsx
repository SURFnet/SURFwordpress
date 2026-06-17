import React from 'react'

const PostType = ({ name, label, onChange, ...rest }) => {
    return (
        <div className="setup-wizard__post-type">
            <input id={`checkbox-${name}`}
                type="checkbox"
                onChange={e => onChange(e.target.checked)}
                {...rest}
            />
            <label htmlFor={`checkbox-${name}`}>{label}</label>
        </div>
    )
}

export default PostType
