/**
 * WordPress imports
 */

import {
    _x,
    getColorClassName,
    getColorObjectByColorValue,
    PanelColorSettings
} from '@surf/js/gutenberg/packages'

const ColorControlPanel = ({ colors, gradients, settings }) => {
    const getColorSlug = (color) => {
        return getColorObjectByColorValue(colors, color)?.slug
    }

    const getGradientSlug = (gradientCSS) => {
        const result = gradients.find((gradient) => gradient.gradient === gradientCSS)
        return result?.slug
    }

    const getClasses = (slug, type) => {
        const colorClass = getColorClassName(type, slug)
        let arr = [colorClass]

        if (type === 'background-color') {
            arr = [
                'has-background-color',
                ...arr
            ]
        }

        if (type === 'gradient-background') {
            arr = [
                'has-gradient-background',
                ...arr
            ]
        }

        return arr.join(' ')
    }

    const setBackground = (value, setting, isGradient) => {
        if (!value) return

        const setAttributes = setting.onChange
        let slug

        if (isGradient) {
            slug = getGradientSlug(value)
        } else {
            slug = getColorSlug(value)
        }

        const background = {
            class: getClasses(slug, setting.type === 'color' ? 'color' : (isGradient ? 'gradient-background' : 'background-color')),
            isGradient,
            slug,
            value
        }

        setAttributes({
            [setting.attribute]: background
        })
    }

    const mappedColorSettings = settings.map(setting => {
        return {
            colorValue: setting.value,
            gradientValue: setting?.type === 'color' ? null : setting.value,
            colors: setting.colors,
            gradients: setting?.type === 'color' ? null : setting?.gradients || null,
            onChange: color => setBackground(color, setting, false),
            onGradientChange: gradient => setBackground(gradient, setting, true),
            label: setting.label
        }
    })

    return (
        <PanelColorSettings
            title={_x('Color Settings', 'admin', 'wp-surf-theme')}
            colorSettings={mappedColorSettings}
        ></PanelColorSettings>
    )
}

export default ColorControlPanel
