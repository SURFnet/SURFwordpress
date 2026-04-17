import '../scss/theme.scss'
import './sprite'

// Polyfills for Popover API and CSS Anchor Positioning
import '@oddbird/popover-polyfill'
import '@oddbird/css-anchor-positioning'

import init from './util/init'
import clientWidth from './components/clientWidth'
import fluidVideos from './components/fluidVideos'
import animations from './components/animations'
import DownloadArchive from './components/downloadArchive'
import AgendaArchive from './components/agendaArchive'
import FaqArchive from '@surf/js/components/faqArchive'
import VacancyArchive from '@surf/js/components/vacancyArchive'
import PostArchive from '@surf/js/components/postArchive'
import headerSearchBar from './components/headerSearchBar'
import hamburger from './components/hamburger'
import accordion from './components/accordion'
import mobileSlider from './components/mobileSlider'
import videoPlayButton from './components/videoPlayButton'
import youtubeConsentWrapper from '@surf/js/components/youtubeConsentWrapper'
import filterPopup from '@surf/js/components/filterPopup'
import fileUploadFields from '@surf/js/components/fileUploadFields'
import WpGallery from './components/wpGallery'
import AssetArchive from '@surf/js/components/assetArchive'
import AssetCategoryArchive from '@surf/js/components/assetCategoryArchive'
import parentMenuItem from '@surf/js/components/parentMenuItem'
import applicationForm from '@surf/js/components/applicationForm'
import pageTopMenu from '@surf/js/components/pageTopMenu'
import assetTopMenu from '@surf/js/components/assetTopMenu'
import toggleSameCheckboxes from './util/toggleSameCheckboxes'
import vacancySingle from './components/vacancySingle'
import SearchArchive from '@surf/js/components/searchArchive'

// On Document ready
init(() => {
    fluidVideos()
    animations()
    clientWidth.init()

    // Add other components here...
    DownloadArchive.init()
    AgendaArchive.init()
    FaqArchive.init()
    VacancyArchive.init()
    PostArchive.init()
    AssetArchive.init()
    AssetCategoryArchive.init()
    SearchArchive.init()
    headerSearchBar()
    hamburger()
    accordion()
    mobileSlider()
    videoPlayButton()
    youtubeConsentWrapper()
    filterPopup()
    fileUploadFields()
    parentMenuItem()
    applicationForm()
    pageTopMenu()
    assetTopMenu()
    toggleSameCheckboxes()
    vacancySingle()

    new WpGallery()
})
