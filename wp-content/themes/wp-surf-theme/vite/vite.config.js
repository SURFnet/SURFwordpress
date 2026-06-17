import autoprefixer from 'autoprefixer'
import eslint from 'vite-plugin-eslint'
import path from 'path'
import postcssPrefixwrap from 'postcss-prefixwrap'
import reload from 'vite-plugin-full-reload'
import { defineConfig } from 'vite'

import blade from './plugins/blade'
import hot from './plugins/hot-file'
import jsxTranslationsPlugin from './plugins/jsx-translations'
import svg from './plugins/svg'
import translations from './plugins/translations'

export default defineConfig({
    server: {
        origin: 'http://localhost:5173'
    },
    base: '/wp-content/themes/wp-surf-theme/dist/',
    publicDir: 'src/static',
    build: {
        manifest: true,
        rollupOptions: {
            input: [
                'src/js/admin.js',
                'src/js/login.js',
                'src/js/theme.js',
                'src/js/theme.surf.js',
                'src/js/theme.powered.js',
                'src/js/editor.js',
                'src/js/editor.surf.js',
                'src/js/editor.powered.js',
                'src/scss/exports.scss'
            ]
        }
    },
    resolve: {
        alias: [
            { find: '@surf/images', replacement: '/images' },
            { find: '@surf', replacement: path.resolve('src') },
            { find: '@fa-pro-svg', replacement: path.resolve('node_modules/@fortawesome/fontawesome-pro/svgs') }
        ]
    },
    css: {
        postcss: {
            plugins: [
                postcssPrefixwrap('.editor-styles-wrapper', { whitelist: ['editor.scss', 'editor.surf.scss', 'editor.powered.scss'] }),
                autoprefixer()
            ]
        },
        preprocessorOptions: {
            scss: {
                includePaths: ['./src/scss']
            }
        }
    },
    plugins: [
        eslint(),
        hot(),
        reload(['*.php', 'views/**/*']),
        blade(),
        jsxTranslationsPlugin({
            srcDir: path.resolve(__dirname, '../src/js'),
            outputFile: path.resolve(__dirname, '../languages/_jsx-strings.js')
        }),
        translations(['languages/*.po'], 'wp-surf-theme'),
        svg({
            resolveSymbolId: (file) => {
                const name = path.basename(file).replace('.svg', '')
                const folder = path.basename(path.dirname(file))
                if (file.includes('fontawesome-pro')) return `fontawesome-pro-${folder}--${name}`
                return `${folder}--${name}`
            }
        })
    ]
})
