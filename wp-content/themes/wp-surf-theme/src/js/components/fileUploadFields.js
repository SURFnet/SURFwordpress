export default function () {
    const fileUploads = document.querySelectorAll('.form-group.file')

    if (!fileUploads.length) {
        return
    }

    fileUploads.forEach(element => {
        const input = element.querySelector('input')
        const fileNamesElement = element.querySelector('.file__file-names')
        input.addEventListener('change', () => {
            let fileList = ''
            for (let i = 0; i < input.files.length; i++) {
                if (fileList !== '') {
                    fileList += ', '
                }
                fileList += input.files[i].name
            }

            fileNamesElement.innerHTML = fileList
        })
    })
}
