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

const loadPo = (file) => {
    return new Promise((resolve, reject) => {
        PO.load(file, (err, po) => {
            if (err) reject(err)
            if (po) resolve(po)
        })
    })
}

/**
 * Checks if a string is used in the built code. Quotes, backslashes and line breaks are escaped there,
 * so the escaped versions are checked too. A short string can match more than needed, which does no harm.
 */
const isUsed = (text, code) => {
    if (!text) return false
    if (code.includes(text)) return true

    const escaped = JSON.stringify(text).slice(1, -1)
    if (code.includes(escaped)) return true

    return code.includes(text.replace(/'/g, "\\'"))
}

/**
 * Collects the code of a chunk and of all chunks it imports, as strings can live in a shared chunk
 */
const collectCode = (chunk, bundle, seen = new Set()) => {
    if (!chunk || seen.has(chunk.fileName)) return ''
    seen.add(chunk.fileName)

    let code = chunk.code || ''
    const imported = [...(chunk.imports || []), ...(chunk.dynamicImports || [])]
    imported.forEach(fileName => {
        code += collectCode(bundle[fileName], bundle, seen)
    })

    return code
}

/**
 * Builds the JSON for wp_set_script_translations(), with only the strings that are used in the code of the entry
 * (instead of all strings of the theme, which made every front-end page about 50 KB larger)
 */
const toJson = (po, code) => {
    const messages = {
        '': {
            domain: po.headers['X-Domain'] ?? 'messages',
            lang: po.headers.Language,
            'plural-forms': po.headers['Plural-Forms'],
        }
    }

    po.items.forEach((item) => {
        if (!isUsed(item.msgid, code) && !isUsed(item.msgid_plural, code)) return

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
        async generateBundle (options, bundle) {
            const files = await getFiles(paths)
            const catalogs = await Promise.all(files.map(async (file) => {
                return {
                    locale: path.basename(file).split('.')[0],
                    po: await loadPo(file)
                }
            }))

            Object.values(bundle).forEach((chunk) => {
                if (chunk.type !== 'chunk' || !chunk.isEntry || !chunk.facadeModuleId) return

                const handle = config.handleResolver(chunk.facadeModuleId)
                const code = collectCode(chunk, bundle)

                catalogs.forEach(({ locale, po }) => {
                    this.emitFile({
                        type: 'asset',
                        fileName: `i18n/${domain}-${locale}-${handle}.json`,
                        source: toJson(po, code)
                    })
                })
            })
        }
    }
}

export default translations
