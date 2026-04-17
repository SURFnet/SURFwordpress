import React from 'react'

import StepPlugins from './StepPlugins'
import StepPostTypes from '@surf/js/admin/components/SetupWizard/StepPostTypes'
import StepSiteInfo from './StepSiteInfo'
import StepTheme from './StepTheme'
import StepTrackingInfo from './StepTrackingInfo'

const { _x } = wp.i18n

const SetupWizard = () => {
    return (
        <>
            <h1>{_x('Setup Wizard', 'admin', 'wp-surf-theme')}</h1>
            <div className="setup-wizard">
                <StepSiteInfo/>
                <StepPostTypes/>
                <StepPlugins/>
                <StepTrackingInfo/>
                <StepTheme/>
            </div>
        </>
    )
}

export default SetupWizard
