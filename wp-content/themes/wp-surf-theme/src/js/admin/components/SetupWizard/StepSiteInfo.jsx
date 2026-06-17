import React, { useState, useEffect } from 'react'
import Step from './Step'
import useFetch, { fetch } from '../../../react/hooks/useFetch'
import { mutate } from 'swr'

const { _x } = wp.i18n

const StepSiteInfo = () => {
    const { data, loading } = useFetch('/wp-json/surf/v1/setup-wizard/site-info')
    const [errors, setErrors] = useState({})
    const [initialized, setInitialized] = useState(false)
    const [updating, setUpdating] = useState(false)

    const [blogname, setBlogname] = useState('')
    const [blogdescription, setBlogdescription] = useState('')
    const [defaultCommentStatus, setDefaultCommentStatus] = useState(false)
    const [commentModeration, setCommentModeration] = useState(false)

    useEffect(() => {
        if (data && !initialized) {
            setBlogname(data.blogname)
            setBlogdescription(data.blogdescription)
            setDefaultCommentStatus(data.default_comment_status)
            setCommentModeration(data.comment_moderation)
            setInitialized(true)
        }
    }, [data, initialized])

    const handleUpdate = async () => {
        setUpdating(true)
        setErrors({})

        const res = await fetch('/wp-json/surf/v1/setup-wizard/site-info', {
            method: 'POST',
            body: JSON.stringify({
                blogname,
                blogdescription,
                default_comment_status: defaultCommentStatus,
                comment_moderation: commentModeration
            })
        })

        if (!res.ok) {
            const data = await res.json()
            setErrors(data?.data?.params)
        }

        await mutate('/wp-json/surf/v1/setup-wizard/site-info')

        setUpdating(false)
    }

    return (
        <Step title={_x('Site info', 'admin', 'wp-surf-theme')} completed={data?.completed}
            loading={loading || updating}>
            {data && (
                <>
                    <div className="step__form-group">
                        <label htmlFor="blogname">{_x('Site Title', 'admin', 'wp-surf-theme')}</label>
                        <input type="text"
                            id="blogname"
                            name="blogname"
                            value={blogname}
                            onChange={(e) => setBlogname(e.target.value)}
                        />
                        {errors.blogname && (
                            <span className="step__error">{errors.blogname}</span>
                        )}
                    </div>

                    <div className="step__form-group">
                        <label htmlFor="blogdescription">{_x('Slogan', 'admin', 'wp-surf-theme')}</label>
                        <input type="text"
                            id="blogdescription"
                            name="blogdescription"
                            value={blogdescription}
                            onChange={(e) => setBlogdescription(e.target.value)}
                        />
                        {errors.blogdescription && (
                            <span className="step__error">{errors.blogdescription}</span>
                        )}
                    </div>

                    <div className="step__form-group">
                        <div className="step__checkbox">
                            <input type="checkbox"
                                id="default_comment_status"
                                name="default_comment_status"
                                checked={defaultCommentStatus}
                                onChange={(e) => setDefaultCommentStatus(e.target.checked)}
                            />
                            <label
                                htmlFor="default_comment_status">{_x('Enable comments by default', 'admin', 'wp-surf-theme')}</label>
                        </div>
                        {errors.comment_moderation && (
                            <span className="step__error">{errors.comment_moderation}</span>
                        )}
                    </div>

                    <div className="step__form-group">
                        <div className="step__checkbox">
                            <input type="checkbox"
                                id="comment_moderation"
                                name="comment_moderation"
                                checked={commentModeration}
                                onChange={(e) => setCommentModeration(e.target.checked)}
                            />
                            <label
                                htmlFor="comment_moderation">{_x('Comment must be manually approved', 'admin', 'wp-surf-theme')}</label>
                        </div>
                        {errors.comment_moderation && (
                            <span className="step__error">{errors.comment_moderation}</span>
                        )}
                    </div>

                    <div className="step__footer">
                        <button className="button button-primary" onClick={handleUpdate} disabled={updating}>
                            {data.completed ? _x('Update', 'admin', 'wp-surf-theme') : _x('Complete', 'admin', 'wp-surf-theme')}
                        </button>
                    </div>
                </>
            )}
        </Step>
    )
}

export default StepSiteInfo
