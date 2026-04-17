import fs from 'fs'
import path from 'path'
import glob from 'glob'

function generateJSXStrings ({ srcDir, outputFile }) {
    const files = glob.sync(`${srcDir}/**/*.jsx`, {
        ignore: ['node_modules/**', 'dist/**']
    })

    let output = '// Auto-generated for Poedit scanning\n\n'
    const seen = new Set()

    const patterns = [
        /_x\s*\(\s*['"`]([^'"`]+)['"`]\s*,\s*['"`]([^'"`]+)['"`]\s*,\s*['"`]([^'"`]+)['"`]\s*\)/g,
        /__\s*\(\s*['"`]([^'"`]+)['"`]\s*,\s*['"`]([^'"`]+)['"`]\s*\)/g,
        /_e\s*\(\s*['"`]([^'"`]+)['"`]\s*,\s*['"`]([^'"`]+)['"`]\s*\)/g,
        /_n\s*\(\s*['"`]([^'"`]+)['"`]\s*,\s*['"`]([^'"`]+)['"`]\s*,\s*\d+\s*,\s*['"`]([^'"`]+)['"`]\s*\)/g
    ]

    // ANSI colors
    const green = '\x1b[32m'
    const gray = '\x1b[90m'
    const reset = '\x1b[0m'
    const bold = '\x1b[1m'

    console.log(`\n${bold}-- Scanning JSX files for translation strings --${reset}\n`)

    const results = []
    let totalUnique = 0
    let longestPath = 0

    files.forEach(file => {
        const content = fs.readFileSync(file, 'utf-8')
        let fileUnique = 0

        patterns.forEach((regex, index) => {
            let match
            while ((match = regex.exec(content)) !== null) {
                let line

                if (index === 0) {
                    const [_, str, ctx, domain] = match
                    line = `_x('${str.replace(/'/g, "\\'")}', '${ctx}', '${domain}');\n`
                } else if (index === 1 || index === 2) {
                    const [_, str, domain] = match
                    line = `__('${str.replace(/'/g, "\\'")}', '${domain}');\n`
                } else if (index === 3) {
                    const [_, single, plural, domain] = match
                    line = `_n('${single.replace(/'/g, "\\'")}', '${plural.replace(/'/g, "\\'")}', 0, '${domain}');\n`
                }

                if (!seen.has(line)) {
                    seen.add(line)
                    output += line
                    fileUnique++
                    totalUnique++
                }
            }
        })

        const relative = path.relative(process.cwd(), file)
        longestPath = Math.max(longestPath, relative.length)

        results.push({
            path: relative,
            count: fileUnique
        })
    })

    // Pretty table output
    results.forEach(r => {
        const paddedPath = r.path.padEnd(longestPath + 2, ' ')
        const label = `${r.count} new strings`

        if (r.count > 0) {
            console.log(`${green}${paddedPath}${label}${reset}`)
        } else {
            console.log(`${gray}${paddedPath}${label}${reset}`)
        }
    })

    fs.writeFileSync(outputFile, output, 'utf-8')

    console.log(`\n${bold}✅ Generated ${path.relative(process.cwd(), outputFile)}${reset}`)
    console.log(`   ${files.length} files scanned`)
    console.log(`   ${green}${totalUnique} unique strings${reset}\n`)
}

/**
 * Vite plugin
 */
export default function jsxTranslationsPlugin (options = {}) {
    const srcDir = options.srcDir || path.join(process.cwd(), 'src/js')
    const outputFile = options.outputFile || path.join(process.cwd(), 'languages/_jsx-strings.js')

    return {
        name: 'jsx-translations',
        buildStart () {
            generateJSXStrings({ srcDir, outputFile })
        }
    }
}

// allow CLI usage
if (import.meta.url === `file://${process.argv[1]}`) {
    generateJSXStrings({
        srcDir: path.join(process.cwd(), 'src/js'),
        outputFile: path.join(process.cwd(), 'languages/_jsx-strings.js')
    })
}
