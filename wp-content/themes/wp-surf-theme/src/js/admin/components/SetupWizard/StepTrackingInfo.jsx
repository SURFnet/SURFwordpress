import React, { useEffect, useState } from 'react'
import Step from './Step'
import useFetch, { fetch } from '../../../react/hooks/useFetch'
import { mutate } from 'swr'

const { _x } = wp.i18n

const StepTrackingInfo = () => {
    const { data, loading } = useFetch('/wp-json/surf/v1/setup-wizard/tracking-info')
    const [initialized, setInitialized] = useState(false)
    const [piwikUrl, setPiwikUrl] = useState('')
    const [piwikId, setPiwikId] = useState('')
    const [errors, setErrors] = useState({})

    const [updating, setUpdating] = useState(false)

    useEffect(() => {
        if (data && !initialized) {
            setPiwikUrl(data.piwik_url)
            setPiwikId(data.piwik_id)
            setInitialized(true)
        }
    }, [data])

    const handleUpdate = async () => {
        setUpdating(true)
        setErrors({})

        const res = await fetch('/wp-json/surf/v1/setup-wizard/tracking-info', {
            method: 'POST',
            body: JSON.stringify({
                piwik_url: piwikUrl,
                piwik_id: piwikId
            })
        })

        if (!res.ok) {
            const data = await res.json()
            setErrors(data?.data?.params)
        }

        await mutate('/wp-json/surf/v1/setup-wizard/tracking-info')
        setUpdating(false)
    }

    return (
        <Step title={_x('Tracking info', 'admin', 'wp-surf-theme')} completed={data?.completed}
            loading={loading || updating}>
            {data && (
                <>
                    <div className="step__form-group">
                        <label htmlFor="containerAddress">{_x('Piwik container URL', 'admin', 'wp-surf-theme')}</label>
                        <input type="text"
                            id="containerAddress"
                            name="containerAddress"
                            value={piwikUrl}
                            onChange={(e) => setPiwikUrl(e.target.value)}
                        />
                        {errors.piwik_url && (
                            <span className="step__error">{errors.piwik_url}</span>
                        )}
                    </div>

                    <div className="step__form-group">
                        <label htmlFor="piwikId">{_x('Piwik site ID', 'admin', 'wp-surf-theme')}</label>
                        <input type="text"
                            id="piwikId"
                            name="piwikId"
                            value={piwikId}
                            onChange={(e) => setPiwikId(e.target.value)}
                        />
                        {errors.piwik_id && (
                            <span className="step__error">{errors.piwik_id}</span>
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

export default StepTrackingInfo
