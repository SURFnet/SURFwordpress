import { exec } from 'child_process'

const compile = () => {
    return new Promise((resolve, reject) => {
        exec('npm run compile-views', (err, stdout, stderr) => {
            if (stdout) resolve(stdout)
            if (stderr) reject(stderr)
        })
    })
}

const blade = () => {
    return {
        name: 'blade',
        async buildEnd () {
            try {
                await compile()
                console.log('Blade views compiled.')
            } catch (e) {
                console.error(e)
            }
        }
    }
}

export default blade
