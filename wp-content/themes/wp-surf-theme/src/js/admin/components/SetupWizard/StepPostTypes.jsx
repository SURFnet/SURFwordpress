import Step from '@surf/js/admin/components/SetupWizard/Step'
import React, { useState } from 'react'
import useFetch, { fetch } from '@surf/js/react/hooks/useFetch'
import PostType from '@surf/js/admin/components/SetupWizard/PostType'

const { _x } = wp.i18n

const StepPostTypes = () => {
    const { data, loading, mutate } = useFetch('/wp-json/surf/v1/setup-wizard/post-types')
    const [updating, setUpdating] = useState(false)

    const handleChange = async (postType, enabled) => {
        setUpdating(true)
        await fetch('/wp-json/surf/v1/setup-wizard/update-post-type', {
            method: 'POST',
            body: JSON.stringify({ post_type: postType, enabled })
        })
        await mutate()
        setUpdating(false)
    }

    return (
        <Step title={_x('Setup post types', 'admin', 'wp-surf-theme')} loading={loading || updating} completed={true}>
            {data && data.map((postType) => (
                <PostType key={postType.name} name={postType.name} label={postType.label}
                    checked={postType.enabled} onChange={v => handleChange(postType.name, v)}
                    disabled={updating}/>
            ))}
        </Step>
    )
}

export default StepPostTypes
