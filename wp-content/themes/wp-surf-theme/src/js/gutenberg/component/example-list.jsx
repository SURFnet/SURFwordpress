/**
 * WordPress imports
 */

import { Component } from '@surf/js/gutenberg/packages'

/**
 * Custom imports
 */

import ExampleListItem from './example-list-item'

/**
 * Example component to show a simple unordered list
 */
export default class ExampleList extends Component {
    render () {
        const { items } = this.props

        if (!items) {
            return ''
        }

        return (
            <ul>
                {items.map((post, index) => {
                    return <ExampleListItem key={index} item={post}/>
                })}
            </ul>
        )
    }
}
