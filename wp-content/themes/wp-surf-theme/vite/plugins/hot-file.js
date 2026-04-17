import path from 'path'
import fs from 'fs'

let exitHandlersBound = false

/**
 * Resolve the dev server URL from the server address and configuration.
 */
const resolveDevServerUrl = (address, config) => {
    const configHmrProtocol = typeof config.server.hmr === 'object' ? config.server.hmr.protocol : null
    const clientProtocol = configHmrProtocol ? (configHmrProtocol === 'wss' ? 'https' : 'http') : null
    const serverProtocol = config.server.https ? 'https' : 'http'
    const protocol = clientProtocol ?? serverProtocol

    const configHmrHost = typeof config.server.hmr === 'object' ? config.server.hmr.host : null
    const configHost = typeof config.server.host === 'string' ? config.server.host : null
    const serverAddress = address.family === 'IPv6' ? `[${address.address}]` : address.address
    const host = configHmrHost ?? configHost ?? serverAddress

    return `${protocol}://${host}:${address.port}`
}

const hotFile = (dir = '', filename = 'hot') => {
    return {
        name: 'hotFile',
        configureServer (server) {
            const hotFile = path.join(server.config.root, dir, filename)

            server.httpServer?.once('listening', () => {
                const address = resolveDevServerUrl(server.httpServer.address(), server.config)
                fs.writeFileSync(hotFile, address)
            })

            if (exitHandlersBound) {
                return
            }

            const clean = () => {
                if (fs.existsSync(hotFile)) {
                    fs.rmSync(hotFile)
                }
            }

            process.on('exit', clean)

            process.on('SIGINT', process.exit)
            process.on('SIGTERM', process.exit)
            process.on('SIGHUP', process.exit)

            exitHandlersBound = true
        }
    }
}

export default hotFile
