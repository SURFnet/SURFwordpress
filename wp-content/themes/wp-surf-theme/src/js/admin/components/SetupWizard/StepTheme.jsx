import React, { useEffect, useState } from 'react'

import Step from './Step'
import useFetch from '../../../react/hooks/useFetch'

const { _x, sprintf } = wp.i18n

const StepTheme = () => {
    const [completed, setCompleted] = useState(false)
    const { data, error } = useFetch(
        '/wp-json/surf/v1/setup-wizard/theme',
        {},
        !completed ? { refreshInterval: 5000 } : {}
    )

    useEffect(() => {
        if (data?.completed) {
            setCompleted(data?.completed)
        }
    }, [data])

    // Only show this step when ACF Pro is active/installed
    if (data && !data.has_settings) {
        return null
    }

    return (
        <Step title={_x('Setup your theme', 'admin', 'wp-surf-theme')} completed={data?.completed}
            loading={!data && !error}>
            <div
                dangerouslySetInnerHTML={{ __html: sprintf(_x('Setup your theme and styling on the <a href=%s target="_blank">Theme Settings</a> page.', 'admin', 'wp-surf-theme'), '/wp-admin/admin.php?page=surf-theme-settings') }}/>
        </Step>
    )
}

export default StepTheme
