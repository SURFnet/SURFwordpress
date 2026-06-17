/**
 * WordPress imports
 */

import { Component } from '@surf/js/gutenberg/packages'

/**
 * Example component to show a list item inside a list
 */
export default class ExampleListItem extends Component {
    render () {
        const { item } = this.props

        if (!item) {
            return ''
        }

        return (
            <li>
                <a href={item.link}>{item.title.rendered}</a>
            </li>
        )
    }
}
