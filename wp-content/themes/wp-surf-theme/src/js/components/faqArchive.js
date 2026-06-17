import AbstractArchive from './abstractArchive'

export default class FaqArchive extends AbstractArchive {
    constructor () {
        super('surf-faq')

        this.setupDownloadHandler()
    }

    setupDownloadHandler () {
        const buttons = document.querySelectorAll('.archive__export-button')

        if (!buttons.length) {
            return
        }

        buttons.forEach(button => {
            const postType = button.dataset.postType
            if (postType !== this.postType) {
                return
            }

            button.addEventListener('click', function () {
                const url = new URL(`/wp-json/surf/v1/export/${postType}`, window.location.origin)

                const output = button.dataset.output
                url.searchParams.set('output', output)

                if (this.form) {
                    const data = new FormData(this.form)

                    for (const [key, value] of data) {
                        if (['', null].includes(value)) {
                            continue
                        }

                        url.searchParams.append(key, value)
                    }
                }

                const taxonomy = button.dataset.taxonomy
                if (!['', null, undefined].includes(taxonomy)) {
                    url.searchParams.set('taxonomy', taxonomy)
                }

                window.open(url.toString(), '_blank')
            })
        })
    }
}
