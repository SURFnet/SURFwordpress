import React from 'react'
import Card from '../Card'
import Spinner from '../Spinner'

const Header = ({ title, completed, loading }) => {
    return (
        <div className="step__header">
            <div className="step__status">
                {completed === true && !loading && (
                    <span className="dashicons dashicons-completed dashicons-yes-alt"></span>
                )}

                {completed === false && !loading && (
                    <span className="dashicons dashicons-marker"></span>
                )}

                {loading && (
                    <Spinner/>
                )}
            </div>
            <h2 className="hndle">{title}</h2>
        </div>
    )
}

const Step = ({ title, children, completed = false, loading = false }) => {
    return (
        <Card className="step"
            header={() => (<Header title={title} completed={completed} loading={loading}/>)}
        >
            {children}
        </Card>
    )
}

export default Step
