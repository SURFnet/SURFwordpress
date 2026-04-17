import useSWR from 'swr'

/**
 * @param {RequestInfo} input
 * @param {RequestInit} init
 */
const fetch = (input, init = {}) => {
    return window.fetch(input, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': customData.restNonce
        },
        ...init
    })
}

const buildFetcher = (args) => {
    return (url) => {
        return fetch(url, args).then(res => res.json())
    }
}

/**
 * @param {string} key
 * @param {RequestInit} fetchOptions
 * @param {SWRConfiguration<Data, Error, BareFetcher<Data>>} swrOptions
 * @returns {SWRResponse<any, any>}
 */
const useFetch = (key, fetchOptions = {}, swrOptions = {}) => {
    const result = useSWR(key, buildFetcher(fetchOptions), swrOptions)
    return { loading: !result.data && !result.error, ...result }
}

export { fetch }
export default useFetch
