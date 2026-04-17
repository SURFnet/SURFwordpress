import Spinner from '../Spinner'
import React, { useState } from 'react'
import { fetch } from '../../../react/hooks/useFetch'
import { mutate } from 'swr'

const { _x } = wp.i18n

const Plugin = ({ plugin }) => {
    const [loading, setLoading] = useState(false)
    const [license, setLicense] = useState('')
    const [error, setError] = useState('')
    const [showDetails, setShowDetails] = useState(false)

    const install = async (plugin) => {
        if (plugin.requires_license && license === '') {
            setError(_x('Please enter a license key', 'admin', 'wp-surf-theme'))
            return
        }

        const response = await fetch('/wp-json/surf/v1/setup-wizard/install-plugin', {
            method: 'POST',
            body: JSON.stringify({ slug: plugin.slug, license })
        })

        if (response.status === 200) {
            setError('')
            return
        }

        const data = await response.json()

        console.log(data)

        setError(data.message)
    }

    const activate = async (plugin) => {
        await fetch(plugin.activation_url)
    }

    const handleClick = async (plugin) => {
        if (plugin.requires_license && !showDetails) {
            setShowDetails(true)
            return
        }

        setLoading(true)
        await install(plugin)
        await activate(plugin)
        await mutate('/wp-json/surf/v1/setup-wizard/plugins')
        setLoading(false)
    }

    return (
        <div className="setup-wizard__plugin-wrapper">
            <div className="setup-wizard__plugin">
                <div className="setup-wizard__plugin-name">
                    {plugin.installed && plugin.active && !loading && (
                        <span className="dashicons dashicons-completed dashicons-yes-alt"></span>
                    )}

                    {(!plugin.installed || !plugin.active) && !loading && (
                        <span className="dashicons dashicons-marker"></span>
                    )}

                    {loading && (
                        <Spinner/>
                    )}
                    {plugin.name}
                </div>

                <div className="setup-wizard__plugin-actions">
                    <button
                        className="button button-secondary"
                        onClick={() => handleClick(plugin)}
                        disabled={(plugin.installed && plugin.active) || loading}
                    >
                        {!plugin.installed && (_x('Install', 'admin', 'wp-surf-theme'))}
                        {plugin.installed && !plugin.active && (_x('Activate', 'admin', 'wp-surf-theme'))}
                        {plugin.installed && plugin.active && (_x('Installed & Activated', 'admin', 'wp-surf-theme'))}
                    </button>
                </div>
            </div>

            {plugin.instructions && (
                <div dangerouslySetInnerHTML={{ __html: plugin.instructions }}></div>
            )}

            {!plugin.installed && plugin.requires_license && showDetails && (
                <form onSubmit={(e) => {
                    e.preventDefault()
                    handleClick(plugin)
                }}>
                    {error && (
                        <div className="setup-wizard__plugin-error notice notice-error">
                            {error}
                        </div>
                    )}
                    <fieldset className="setup-wizard__plugin-license">
                        <label htmlFor={plugin.slug}>
                            {_x('License key:', 'admin', 'wp-surf-theme')}
                        </label>
                        <input
                            className="regular-text"
                            autoFocus={true}
                            type="text"
                            style={{ minWidth: '30em', width: 'calc(100% - 12em)' }}
                            id={plugin.slug}
                            value={license}
                            onChange={(e) => setLicense(e.target.value)}
                        />
                    </fieldset>
                </form>
            )}
        </div>
    )
}

export default Plugin
