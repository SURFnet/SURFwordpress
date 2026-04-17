import path from 'path'
import glob from 'glob'
import PO from 'pofile'

const defaultConfig = {
    handleResolver: (id) => {
        const parts = path.basename(id).split('.')
        const basename = parts.slice(0, -1).join('.')
        return `surf.${basename}`
    }
}

const toJson = async (file) => {
    const po = await new Promise((resolve, reject) => {
        PO.load(file, (err, po) => {
            if (err) reject(err)
            if (po) resolve(po)
        })
    })

    const messages = {
        '': {
            domain: po.headers['X-Domain'] ?? 'messages',
            lang: po.headers.Language,
            'plural-forms': po.headers['Plural-Forms'],
        }
    }

    po.items.forEach((item) => {
        const key = item.msgctxt !== null ? `${item.msgctxt}\u0004${item.msgid}` : item.msgid
        messages[key] = item.msgstr
    })

    return JSON.stringify({
        domain: po.headers['X-Domain'] || 'messages',
        locale_data: {
            messages,
        }
    })
}

const getFiles = async (paths) => {
    const promises = paths.map(path => {
        return new Promise((resolve, reject) => {
            glob(path, {}, (err, files) => {
                if (err) reject(err)
                resolve(files)
            })
        })
    })

    const results = await Promise.all(promises)
    return results.flat()
}

const translations = (paths, domain, userConfig = {}) => {
    const config = { ...defaultConfig, ...userConfig }

    return {
        name: 'translations',
        async moduleParsed (info) {
            if (!info.isEntry) return

            const handle = config.handleResolver(info.id)
            const files = await getFiles(paths)
            const promises = files.map(async (file) => {
                const json = await toJson(file)
                const locale = path.basename(file).split('.')[0]

                return {
                    type: 'asset',
                    fileName: `i18n/${domain}-${locale}-${handle}.json`,
                    source: json
                }
            })

            const results = await Promise.all(promises)

            results.forEach(this.emitFile)
        }
    }
}

export default translations
