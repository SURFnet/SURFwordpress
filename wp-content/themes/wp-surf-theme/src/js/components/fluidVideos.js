export default function () {
    const videos = document.querySelectorAll('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]')

    videos.forEach(video => {
        const videoContainer = document.createElement('div')

        videoContainer.classList.add('video-container')
        video.parentNode.replaceChild(videoContainer, video)
        videoContainer.appendChild(video)
        videoContainer.classList.add('active')
    })
}
