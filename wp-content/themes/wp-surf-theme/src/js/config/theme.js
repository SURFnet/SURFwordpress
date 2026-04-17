const current = () => {
    return surf.currentTheme ?? 'surf'
}

const themes = () => {
    return surf.themes ?? []
}

const is = (theme) => {
    return current() === theme
}

const SURF = 'surf'
const POWERED_BY_SURF = 'powered'

export default { current, themes, is, SURF, POWERED_BY_SURF }
