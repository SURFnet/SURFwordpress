import qs from 'query-string'

/**
 * Check if IntersectionObserver is natively enabled.
 * @return {Boolean} True if IntersectionObserver is enabled, false otherwise.
 */
export const isIntersectionObserverEnabled = () => 'IntersectionObserver' in window

/**
 * Set up an IntersectionObserver.
 *
 * Accepts a selector or an array of selectors, and executes the callback
 * function for each selector when it enters the viewport.
 *
 * For more info, see: <https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API>
 *
 * @param {DOMString|Array} selectors HTML selector for the observer.
 * @param {Function} callback Function to run when an element enters the viewpoint.
 * @param {Array} threshold Threshold to trigger the IntersectionObserver.
 */
export const setupIntersectionObserver = (selectors, callback, threshold = [0]) => {
    const io = new IntersectionObserver(
        entries => {
            [...entries].forEach(({ isIntersecting, _, target }) => {
                if (isIntersecting) {
                    callback(target)
                    io.unobserve(target)
                }
            })
        },
        { threshold }
    )

    if (!selectors || !callback) {
        return
    }

    for (const selector of [].concat(selectors)) {
        for (const element of [...document.querySelectorAll(selector)]) {
            io.observe(element)
        }
    }
}

/**
 * Set up an AJAX call using Fetch.
 * @param {string} url
 * @param {Object} params Params to send to the Ajax call.
 * @param {string} method
 * @return {Promise}
 */
export const ajax = (url, params, method = 'POST') => {
    const body = qs.stringify(
        {
            ...params
        },
        { arrayFormat: 'index' }
    )
    const args = {
        method,
        credentials: 'same-origin',
        headers: new Headers({ 'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8' })
    }
    if (method === 'GET') {
        url += '?' + body
    } else {
        args.body = body
    }
    return fetch(url, args)
}

export const removeEmptyValues = (object) => {
    return Object.entries(object).reduce((prev, [key, value]) => {
        if (typeof value === 'undefined') {
            return prev
        }

        prev[key] = value
        return prev
    }, {})
}

/**
 * Sanitizes parameter values for use in URLs / UI state
 * Not a replacement for server-side sanitization
 * @param value
 * @returns {*|string|number|boolean}
 */
export const sanitizeParam = (value) => {
    if (value === null || value === undefined) {
        return value
    }

    if (Array.isArray(value)) {
        // Sanitize each item recursively
        return value.map(item => sanitizeParam(item))
    }

    if (typeof value === 'number' || typeof value === 'boolean') {
        return value
    }

    if (typeof value === 'string') {
        // Strip HTML-significant characters
        return value.replace(/[<>"]/g, '')
    }

    if (typeof value === 'object') {
        // Objects should never end up in URL/UI state
        console.warn('Unexpected object in URL params:', value)
        return null
    }

    // Fallback: stringify unknown types safely
    return String(value).replace(/[<>"]/g, '')
}
