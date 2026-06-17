import { unregisterBlockStyle } from '@surf/js/gutenberg/packages'

//
// Block styles to unregister
const gutenbergStylesToUnregister = [
    ['core/button', 'fill'],
    ['core/button', 'outline'],
    ['core/image', 'rounded'],
    ['core/pullquote', 'solid-color'],
    ['core/quote', 'large'],
    ['core/quote', 'plain'],
    ['core/separator', 'dots'],
    ['core/separator', 'wide'],
    ['core/table', 'stripes']
]

//
// Init
export default () => {
    gutenbergStylesToUnregister.forEach(style => {
        unregisterBlockStyle(style[0], style[1])
    })
}
