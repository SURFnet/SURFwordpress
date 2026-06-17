import { addFilter } from './packages'
import Cookie from 'js-cookie'

addFilter('blocks.registerBlockType', 'surf', (block, blockName) => {
    if (!block.supports) {
        block.supports = {}
    }

    if (['surf/cta'].includes(blockName)) {
        block.supports.color = {
            text: true,
            background: true
        }
    }

    if (['core/heading'].includes(blockName)) {
        block.supports.color = {
            text: true,
            background: false
        }
    }

    if (blockName === 'core/table') {
        block.supports.color = false
    }

    if (blockName === 'core/embed') {
        const allowedEmbeds = ['youtube', 'vimeo']
        block.variations = block.variations.filter(v => allowedEmbeds.includes(v.name))
    }

    return block
})

setInterval(() => {
    let notice = Cookie.get('pdf_index_notice')

    if (notice) {
        notice = JSON.parse(notice)
        const { createWarningNotice, createSuccessNotice } = wp.data.dispatch(wp.notices.store)

        if (notice.status === 'indexed') {
            createSuccessNotice(notice.message, { type: 'snackbar' })
        } else {
            createWarningNotice(notice.message, { type: 'snackbar' })
        }

        Cookie.remove('pdf_index_notice', { path: '/wp-admin', SameSite: 'Strict' })
    }
}, 1000)
