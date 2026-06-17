/**
 * Get the featured image from the REST API post object
 * @param {Object} item
 * @param {String} size
 * @returns {String}
 */
export const getImage = (item, size) => {
    if (typeof item._embedded === 'undefined') {
        return ''
    }
    if (typeof item._embedded['wp:featuredmedia'] === 'undefined') {
        return ''
    }
    if (typeof item._embedded['wp:featuredmedia'][0] === 'undefined') {
        return ''
    }

    const sizes = item._embedded['wp:featuredmedia'][0].media_details.sizes

    if (typeof sizes[size] === 'undefined') {
        return sizes.full
    }

    return sizes[size]
}

/**
 * Get the title from the REST API post object
 * @param {Object} item
 * @returns {String}
 */
export const getTitle = item => {
    if (typeof item.title === 'object') {
        return item.title.raw
    }

    return item.title
}

/**
 * Get the title from the REST API post object
 * @param {Object} item
 * @returns {String}
 */
export const getPostDate = item => {
    return item.date
}

/**
 * Get the author name from the REST API post object
 * @param {Object} item
 * @returns {String}
 */
export const getAuthorName = item => {
    if (typeof item._embedded === 'undefined') {
        return ''
    }
    if (typeof item._embedded.author === 'undefined') {
        return ''
    }
    if (typeof item._embedded.author[0] === 'undefined') {
        return ''
    }

    const name = item._embedded.author[0].name

    if (typeof name === 'undefined') {
        return ''
    }

    return name
}

/**
 * Get REST API filter arg by taxonomy slug
 * @param {String} taxonomySlug
 * @returns {String}
 */
export const getRestArgByTaxonomySlug = taxonomySlug => {
    const restArgs = {
        category: 'categories',
        post_tag: 'tags'
    }

    return restArgs[taxonomySlug] || taxonomySlug
}
