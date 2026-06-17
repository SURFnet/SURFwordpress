import React, { useMemo } from 'react'
import Step from './Step'
import useFetch from '../../../react/hooks/useFetch'
import Plugin from './Plugin'

const { _x } = wp.i18n

const StepPlugins = () => {
    const { data, loading } = useFetch('/wp-json/surf/v1/setup-wizard/plugins')
    const completed = useMemo(() => {
        return data && data.filter(p => p.installed).length >= 1
    }, [data])

    return (
        <Step title={_x('Setup plugins', 'admin', 'wp-surf-theme')} loading={loading} completed={completed}>
            {data && data.map(plugin => (
                <Plugin key={plugin.slug} plugin={plugin}/>
            ))}
        </Step>
    )
}

export default StepPlugins
