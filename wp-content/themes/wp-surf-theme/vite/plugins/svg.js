import { optimize } from 'svgo'
import path from 'path'
import fs from 'fs'
import * as crypto from 'crypto'

const fileRegex = /\.svg$/

const defaultConfig = {
    domId: 'svg-sprite',
    symbolId: '[name]',
    resolveSymbolId: null,
    spriteName: 'sprite.svg'
}

const readFile = (filepath) => {
    return new Promise((resolve, reject) => {
        fs.readFile(filepath, (err, buffer) => {
            if (err) {
                return reject(err)
            }

            resolve(buffer)
        })
    })
}

const svg = (userConfig = {}) => {
    const virtualModuleId = 'virtual:svg'
    const resolvedVirtualModuleId = '\0' + virtualModuleId

    const config = { ...defaultConfig, ...userConfig }

    return {
        name: 'svg',
        resolveId (id) {
            if (id === virtualModuleId) {
                return resolvedVirtualModuleId
            }
        },
        load (id) {
            if (id === resolvedVirtualModuleId) {
                return `
                    export default (symbol) => {
                        if (!document.querySelector('svg#${config.domId}')) {
                            const container = document.createElement('div')
                            container.innerHTML = '<svg id="${config.domId}" xmlns="http://www.w3.org/2000/svg" style="display: none"><defs></defs></svg>'
                            document.body.prepend(container.firstChild)
                        }
                    
                        document.querySelector('svg#${config.domId} defs').insertAdjacentHTML('beforeend', symbol)
                    }
                `.trim()
            }
        },
        async transform (src, id) {
            if (!fileRegex.test(id)) {
                return null
            }

            const buffer = await readFile(id)
            const { data } = await optimize(buffer.toString())

            const filename = path.basename(id).replace('.svg', '')
            const folder = path.basename(path.dirname(id))
            const hash = crypto.createHash('sha256').update(data).digest('hex').slice(0, 6)
            const symbolId = typeof config.resolveSymbolId === 'function'
                ? config.resolveSymbolId(id)
                : config.symbolId
                    .replace('[name]', filename)
                    .replace('[folder]', folder)
                    .replace('[hash]', hash)

            const symbol = data.replace('<svg', `<symbol id="${symbolId}"`).replace('</svg>', '</symbol>').trim()

            return {
                code: `
                   import addSymbol from 'virtual:svg'
                   addSymbol(${JSON.stringify(symbol)})
                   export default ${JSON.stringify(symbolId)}
                `.trim(),
                map: null
            }
        }
    }
}

export default svg
