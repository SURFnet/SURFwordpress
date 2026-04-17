import { ajax, sanitizeParam } from '../util/helpers'
import animations from './animations'

/**
 * AbstractArchive
 *
 * @class AbstractArchive
 * @abstract
 */
export default class AbstractArchive {
    /** @private */
    static _instance

    /**
     * ========================================================================================
     * INIT Functions
     * ========================================================================================
     */

    /**
     * Initialises the archive class
     * @returns {*}
     */
    static init () {
        if (!this._instance) this._instance = new this()
        return this._instance
    }

    /**
     * Checks if Class is called correctly
     * @param {String} postType
     */
    constructor (postType) {
        if (this.constructor === AbstractArchive) throw new Error('Abstract class can\'t be instantiated.')
        if (postType === undefined) throw new Error('You must set the \'postType\' variable.')
        if (!postType) throw new Error('The \'postType\' variable can\'t be empty')

        this.postType = postType
        this.startup()
    }

    /**
     * Checks if this class should be used on the current page.
     * And starts up the class if that is the case
     */
    startup () {
        this.archive = document.querySelector(`#archive-${this.postType}`)
        if (!this.archive) {
            return
        }

        this.setProperties()
        this.resetUrl()

        window.onpopstate = this.handlePoppedState.bind(this)
    }

    /**
     * Sets up default values
     */
    setProperties () {
        console.dir('setProperties')
        this.isLoading = false
        this.filterItems = {}
        this.filterTimers = {}

        this.form = this.archive.querySelector('form.archive__form')

        if (!this.hasValidForm()) return

        // Sidebar filters
        this.fieldsets = this.form.querySelectorAll('fieldset')
        if (this.fieldsets) {
            this.fieldsets.forEach(this.setFieldsetFilterItem.bind(this))
        }
        console.dir('this.fieldsets')
        console.dir(this.fieldsets)

        // Content Items
        this.posts = this.archive.querySelector('.archive__content')
        this.foundPosts = this.archive.querySelector('.found_item_count')
        this.pagination = this.archive.querySelector('.archive__pagination')
        this.filterCounts = this.archive.querySelectorAll('.archive-filter__count')
        this.setPaginationListeners()
    }

    /**
     * ========================================================================================
     * Filter related functionality
     * ========================================================================================
     */

    /**
     * Get pagination if exists and add listeners
     */
    setPaginationListeners () {
        this.paginationItems = this.pagination ? this.pagination.querySelectorAll('a') : null
        if (this.paginationItems) {
            this.paginationItems.forEach(this.setPaginationListener.bind(this))
        }
    }

    /**
     * Add actual listener to pagination item
     * @param {HTMLAnchorElement} paginationItem
     */
    setPaginationListener (paginationItem) {
        paginationItem.addEventListener('click', this.paginationItemListener.bind(this, paginationItem))
    }

    /**
     * Handles the pagination functionality
     * @param {HTMLAnchorElement} paginationItem
     * @param {Event} e
     */
    paginationItemListener (paginationItem, e) {
        e.preventDefault()

        const url = new URL(paginationItem.href)
        const page = url.searchParams.get('paged') ? url.searchParams.get('paged') : 1
        this.updatePagination(page)
        this.getPosts()
    }

    /**
     * Sets filter item values and adds listeneres to fieldsets
     * @param {HTMLFieldSetElement} fieldset
     */
    setFieldsetFilterItem (fieldset) {
        const name = fieldset.dataset.name

        if (this.filterItems[name] === undefined) {
            this.filterItems[name] = {
                name,
                value: [],
            }

            if (fieldset.querySelector('[type="text"]')) {
                fieldset.addEventListener('input', this.fieldsetFilterChangeListener.bind(this, fieldset, 300))
            } else {
                fieldset.addEventListener('change', this.fieldsetFilterChangeListener.bind(this, fieldset))
            }
        }

        this.filterItems[name].value = null

        const inputItems = fieldset.querySelectorAll('[name]')
        if (!inputItems) {
            return
        }

        inputItems.forEach(inputItem => {
            if (inputItem.nodeName === 'INPUT') {
                switch (inputItem.type) {
                case 'checkbox':
                    if (!inputItem.checked) break

                    if (!Array.isArray(this.filterItems[name].value)) {
                        this.filterItems[name].value = []
                    }

                    this.filterItems[name].value.push(inputItem.value)
                    break

                case 'radio':
                    if (!inputItem.checked) break

                    this.filterItems[name].value = inputItem.value
                    break

                default:
                    this.filterItems[name].value = inputItem.value
                    break
                }
            } else if (inputItem.nodeName === 'SELECT') {
                const options = inputItem.querySelectorAll('option')
                if (!options) {
                    return
                }

                options.forEach(optionItem => {
                    if (optionItem.selected) {
                        this.filterItems[name].value = optionItem.value
                    }
                })
            }
        })
    }

    /**
     * Handles filter listeners and adds a delay if a timeout is passed
     * @param {HTMLFieldSetElement} fieldset
     * @param {int} timeout
     */
    fieldsetFilterChangeListener (fieldset, timeout) {
        if (timeout) {
            const name = fieldset.dataset.name
            if (this.filterTimers[name] !== undefined) clearTimeout(this.filterTimers[name])

            this.filterTimers[name] = setTimeout(() => {
                this.setFieldsetFilterItem(fieldset)
                this.handleFiltersUpdated()
            }, timeout)
        } else {
            this.setFieldsetFilterItem(fieldset)
            this.handleFiltersUpdated()
        }
    }

    /**
     * Fires when filters are updated
     */
    handleFiltersUpdated () {
        this.updateUrlAndParams()
        this.getPosts()
    }

    /**
     * Handles page load when user goes back and forward through history
     */
    handlePoppedState () {
        const location = document.location.href
        if (!location || !location.includes(this.form.action)) {
            return
        }

        // TODO: handle it with api calls instead of reloads
        document.location.reload()
    }

    /**
     * ========================================================================================
     * URL related functionality
     * ========================================================================================
     */

    /**
     * Resets url
     */
    resetUrl () {
        if (!this.hasValidForm()) return

        const newUrl = this.form.action
        this.url = new URL(newUrl)
        this.paged = 1
    }

    /**
     * Changes url to correspond with selected filters
     */
    updateUrlAndParams () {
        this.resetUrl()
        this.params = {}

        if (this.filterItems) {
            for (const [name, item] of Object.entries(this.filterItems)) {
                const itemValue = item.value
                const isSearchParam = (this.postType === 'search' && name === 's')
                const hasValue = itemValue !== null && itemValue !== undefined && (itemValue !== '' || isSearchParam)
                if (!hasValue) {
                    continue
                }

                const cleanedValue = sanitizeParam(itemValue)
                if (cleanedValue === null || cleanedValue === undefined) {
                    continue
                }

                if (Array.isArray(cleanedValue)) {
                    if (cleanedValue.length === 0) {
                        continue
                    }

                    cleanedValue.forEach(value => {
                        this.url.searchParams.append(name, String(value))
                    })
                    this.params[name] = cleanedValue
                    continue
                }

                this.url.searchParams.set(name, String(cleanedValue))
                this.params[name] = cleanedValue
            }
        }

        history.pushState(
            {
                url: this.url.origin,
                params: this.url.search,
            },
            null, this.url.href,
        )

        this.addInvisibleParams()
    }

    /**
     * @overwrite to add parameters to the API call that won't be represented in the URL
     */
    addInvisibleParams () {
        // Overwrite if necessary
    }

    /**
     * Handles pagination in the URL structure
     * @param {int} page
     */
    updatePagination (page = 1) {
        this.url.href = this.form.action + this.url.search
        this.paged = page

        if (page > 1) {
            this.url.pathname = this.url.pathname.replace(/\/?$/, '/')
            this.url.pathname += `page/${page}/`
        }

        history.pushState(
            {
                url: this.url.origin,
                params: this.url.search,
            },
            null, this.url.href,
        )
    }

    /**
     * ========================================================================================
     * API related functionality
     * ========================================================================================
     */

    /**
     * Update all the filter counts
     * @param filterCounts
     */
    setFilterCounts (filterCounts) {
        this.filterCounts.forEach(item => {
            const filterFor = item.getAttribute('for')

            if (!filterFor) {
                return
            }

            const filterCount = filterCounts[filterFor]
            const parentEl = item.closest('.archive-filter__item')
            const countEl = item.querySelector('.archive-filter__count__current')

            if (!parentEl || !countEl) {
                return
            }

            countEl.innerHTML = filterCount || 0
        })
    }

    /**
     * Makes the API call and updates the content
     * @returns {Promise<void>}
     */
    async getPosts () {
        this.setLoading()

        // Do the call
        try {
            await (await ajax(
                window.customData.apiBaseURL + this.postType,
                {
                    paged: this.paged,
                    ...this.params,
                    lang: window.customData.lang,
                }, 'GET').then(response => {
                return response.json()
            }).then(response => {
                const html = response.html
                const foundPosts = response.found_posts
                const pagination = response.pagination
                const filterCounts = response.filter_counts

                if (html !== undefined && this.posts) {
                    this.posts.innerHTML = html

                    setTimeout(() => {
                        animations()
                    }, 200)
                }

                if (foundPosts !== undefined && this.foundPosts) {
                    this.foundPosts.innerHTML = foundPosts
                }

                if (pagination !== undefined && this.pagination) {
                    this.pagination.innerHTML = pagination
                    this.setPaginationListeners()
                }

                if (filterCounts !== undefined && this.filterCounts) {
                    this.setFilterCounts(filterCounts)
                }

                // Custom for FAQ
                const searchTitleElement = document.getElementById('search-title')
                const searchHeader = document.querySelector('.archive__search-header')
                if (typeof this.params.search !== 'undefined') {
                    if (searchHeader) {
                        searchHeader.classList.add('show')
                        searchTitleElement.textContent = this.params.search
                    }
                } else {
                    if (searchHeader) {
                        searchHeader.classList.remove('show')
                        searchTitleElement.innerHTML = ''
                    }
                }

                this.setLoading(false)
            }))
        } catch (error) {
            this.setLoading(false)
        }
    }

    /**
     * ========================================================================================
     * Extra's
     * ========================================================================================
     */

    /**
     * Tracks if the archive is currently busy with an API call
     * @param {boolean} isLoading
     */
    setLoading (isLoading = true) {
        this.isLoading = isLoading

        if (isLoading) {
            document.body.classList.add('loading')
        } else {
            document.body.classList.remove('loading')
        }

        this.toggleFilters()
    }

    /**
     * Disables and enables fieldsets if the archive is loading or not
     */
    toggleFilters () {
        if (this.fieldsets) {
            this.fieldsets.forEach(this.toggleFieldset.bind(this))
        }
    }

    /**
     * Toggles individual fieldsets if the archive is loading or not
     * @param fieldset
     */
    toggleFieldset (fieldset) {
        const inputs = fieldset.querySelectorAll('input, select')

        if (inputs) {
            inputs.forEach((input) => {
                if (input.nodeName !== 'INPUT' || input.type !== 'text') input.disabled = this.isLoading
            })
        }
    }

    hasValidForm () {
        return this.form && this.form.action
    }
}
