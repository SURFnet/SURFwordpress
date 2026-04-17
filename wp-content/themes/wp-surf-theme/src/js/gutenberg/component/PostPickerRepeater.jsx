import PropTypes from 'prop-types'
import { arrayMoveImmutable } from 'array-move'

import {
    _x,
    Button
} from '@surf/js/gutenberg/packages'

import PostPickerModal from './PostPickerModal'

const PostPickerRepeater = ({ onChange, posts, postTypes = ['post'], taxonomies = [] }) => {
    const handleSelect = post => {
        const postExists = posts.find(item => item.id === post.id)
        onChange([
            ...posts,
            ...(postExists ? [] : [post])
        ])
    }

    const handleRemove = post => {
        onChange(posts.filter(item => item.id !== post.id))
    }

    const handleOrder = ({ oldIndex, newIndex }) => {
        onChange(arrayMoveImmutable(posts, oldIndex, newIndex))
    }

    const PostItem = ({ index, post, total }) => (
        <li className='post-sorter-item'>
            <div className='post-sorter-item__title'>{post.title.rendered}</div>
            <div className='post-sorter-item__buttons'>
                <Button isDestructive isSmall
                    onClick={() => handleRemove(post)}>{_x('Remove', 'admin', 'wp-surf-theme')}</Button>
                {index !== 0 && (
                    <Button
                        icon='arrow-up-alt2'
                        isSecondary
                        isSmall
                        label={_x('Move up', 'admin', 'wp-surf-theme')}
                        onClick={() => handleOrder({ oldIndex: index, newIndex: index - 1 })}
                    />
                )}
                {index !== total - 1 && (
                    <Button
                        icon='arrow-down-alt2'
                        isSecondary
                        isSmall
                        label={_x('Move down', 'admin', 'wp-surf-theme')}
                        onClick={() => handleOrder({ oldIndex: index, newIndex: index + 1 })}
                    />
                )}
            </div>
        </li>
    )

    const PostList = ({ posts }) => (
        <ol className='post-sorter'>
            {
                posts.map((post, index) => <PostItem index={index} post={post} key={post.id} total={posts.length}/>)
            }
        </ol>
    )

    return (
        <>
            <PostList posts={posts}/>
            <PostPickerModal closeOnSelect onSelect={post => handleSelect(post)} postTypes={postTypes}
                taxonomies={taxonomies}>
                {
                    toggleModal => <Button isDefault
                        onClick={toggleModal}>{_x('Select a post', 'admin', 'wp-surf-theme')}</Button>
                }
            </PostPickerModal>
        </>
    )
}

PostPickerRepeater.propTypes = {
    onChange: PropTypes.func,
    posts: PropTypes.array
}

PostPickerRepeater.defaultProps = {
    onChange: () => {
    },
    posts: []
}

export default PostPickerRepeater
