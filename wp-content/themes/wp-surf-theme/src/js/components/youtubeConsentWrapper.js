import fluidVideos from '@surf/js/components/fluidVideos'

const youtubeConsentWrapper = (consent = 'custom_consent') => {
    if (!window.ppms) {
        showIframe()
    }

    if (window.ppms) {
        update(consent)

        document.addEventListener('click', (e) => {
            if (!e.target.matches('.youtube-consent-wrapper__open')) return
            ppms.cm.api('openConsentForm')
        })

        document.addEventListener('click', (e) => {
            if (!e.target.matches('#ppms_cm_save-choices') && !e.target.matches('#ppms_cm_agree-to-all')) return
            update(consent)
        })
    }
}

const showIframe = () => {
    // Show iframe
    document.querySelectorAll('.youtube-consent-wrapper__template').forEach((element) => {
        const clone = element.content.cloneNode(true)
        element.after(clone)
    })

    // Hide placeholder
    document.querySelectorAll('.youtube-consent-wrapper__placeholder').forEach((element) => {
        element.classList.add('hidden')
    })

    fluidVideos()
}

const showNotice = () => {
    // Show notice in placeholder
    document.querySelectorAll('.youtube-consent-wrapper__notice').forEach(element => {
        element.classList.remove('hidden')
    })
}

const update = (consent) => {
    if (!window.ppms) return

    ppms.cm.api('getComplianceSettings', (settings) => {
        const consentData = settings.consents[consent] ?? null

        // Consent not given
        if (consentData === null || consentData.status < 1) {
            showNotice()
        }

        // Consent given
        if (consentData && consentData.status === 1) {
            showIframe()
        }
    })
}

export default youtubeConsentWrapper
