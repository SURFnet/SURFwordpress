export default function () {
    const videoWrapper = document.querySelectorAll('.wp-block-video')

    const formatVideoPlayButton = (videoElement) => {
        videoElement.insertAdjacentHTML('beforeend', '<svg class="video-play" viewBox="0 0 54 64" xmlns="http://www.w3.org/2000/svg"><path d="M50.766 26.234A6.772 6.772 0 0 1 54 32c0 2.348-1.223 4.528-3.234 5.64L10.27 62.39c-2.084 1.391-4.693 1.447-6.822.252A6.753 6.753 0 0 1 0 56.75V7.25a6.753 6.753 0 0 1 3.448-5.888 6.753 6.753 0 0 1 6.822.128l40.496 24.744z" fill="#FFF"/></svg>')
    }

    videoWrapper.forEach((element) => {
        const video = element.querySelector('video')
        if (element.contains(video)) {
            formatVideoPlayButton(element)
            const videoPlayButton = element.querySelector('.video-play')
            video.classList.add('has-media-controls-hidden')
            videoPlayButton.addEventListener('click', () => {
                video.play()
                videoPlayButton.classList.add('is-hidden')
                video.classList.remove('has-media-controls-hidden')
                video.setAttribute('controls', 'controls')
            })
        }
    })
}
