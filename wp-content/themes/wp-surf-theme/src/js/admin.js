import '../scss/admin.scss'

import ComponentLoader from './util/ComponentLoader'
import * as components from './admin/components'
import openAcfTab from './admin/utils/open-acf-tab'
import widgetHelper from '@surf/js/admin/components/widgetHelper'

document.addEventListener('DOMContentLoaded', () => {
    const tab = new URL(location).searchParams.get('acf-tab')
    if (tab) openAcfTab(tab)

    ComponentLoader.init(components)

    widgetHelper.init()
})
