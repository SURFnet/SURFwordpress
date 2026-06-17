const openAcfTab = (tab) => {
    const el = document.querySelector(`[data-key="${tab}"]`)
    if (el) el.click()
}

export default openAcfTab
