import {
    _x,
    date,
    withSelect
} from '@surf/js/gutenberg/packages'

import { getAuthorName } from '../helper/rest-api-helper'

const applyWithSelect = withSelect((select, { searchQuery, postType, args }) => ({
    searchResults: select('core').getEntityRecords('postType', postType || 'post', {
        ...{
            per_page: 10,
            search: searchQuery || '',
            _embed: 1
        },
        ...args
    })
}))

const PostPickerList = ({ onSelect, searchResults }) => {
    return (
        <>
            {
                searchResults
                    ? (
                        <ol className='post-picker-list'>
                            {
                                searchResults.map((post, index) => (
                                    <li className='post-picker-list__item' key={post.id}>
                                        <button className='post-picker-list__item-button'
                                            onClick={() => onSelect(post)} type='button'>
                                            <div className='post-picker-list__item-title'>{post.title.rendered}</div>
                                            <div className='post-picker-list__item-meta'>{date(surf.date.format, post.date)}, {getAuthorName(post)}</div>
                                        </button>
                                    </li>
                                ))
                            }
                        </ol>
                    )
                    : <p>{_x('Loading...', 'admin', 'wp-surf-theme')}</p>
            }
        </>
    )
}

export default applyWithSelect(PostPickerList)
