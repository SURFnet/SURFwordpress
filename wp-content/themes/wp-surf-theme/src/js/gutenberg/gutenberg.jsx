import './hooks'
import SurfIcon from './icon/surfIcon'
import initBlockStyles from './config/blockStyles'
import Header from './block/header'
import Hero from './block/hero'
import Events from './block/events'
import Articles from './block/articles'
import Downloads from './block/downloads'
import FeaturedItems from './block/featured-items'
import Cta from './block/cta'
import Separator from './block/separator'
import Accordion from './block/accordion'
import StyleGroup from './block/style-group'
import LatestVacancies from '@surf/js/gutenberg/block/latest-vacancies'
import Roadmap from '@surf/js/gutenberg/block/roadmap'
import Step from '@surf/js/gutenberg/block/roadmap/step'
import ContactPerson from './block/contact-person'
import CustomCards from '@surf/js/gutenberg/block/custom-cards'
import Card from '@surf/js/gutenberg/block/custom-cards/card'
import RelatedAssets from '@surf/js/gutenberg/block/related-assets'
import AssetExamples from '@surf/js/gutenberg/block/asset-examples'
import Sitemap from '@surf/js/gutenberg/block/sitemap'
import './block/image'
import './block/button'

const { _x } = wp.i18n
const { setCategories, getCategories } = wp.blocks

//
// unregisterBlockStyle doesn't work inside wp.domReady
// https://github.com/WordPress/gutenberg/issues/25330

window.onload = () => {
    initBlockStyles()
}

// Add SURF block category with custom icon
setCategories([
    {
        slug: 'surf',
        title: _x('SURF', 'admin', 'wp-surf-theme'),
        icon: <SurfIcon/>
    },
    ...getCategories().filter(({ slug }) => slug !== 'surf')
])

// Register blocks
Header()
Hero()
Events()
Articles()
Downloads()
FeaturedItems()
Cta()
Separator()
Accordion()
LatestVacancies()
StyleGroup()
Roadmap()
Step()
ContactPerson()
CustomCards()
Card()
RelatedAssets()
AssetExamples()
Sitemap()
