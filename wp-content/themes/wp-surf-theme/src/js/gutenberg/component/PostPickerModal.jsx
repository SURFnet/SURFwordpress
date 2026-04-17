import {
    useState,
    _x,
    Modal,
    TextControl
} from '@surf/js/gutenberg/packages'
import PropTypes from 'prop-types'

import PostPickerList from './PostPickerList'
import PostTaxonomyFilter from './PostTaxonomyFilter'
import PostTypeFilter from './PostTypeFilter'

import { getRestArgByTaxonomySlug } from '../helper/rest-api-helper'

const PostPickerModal = ({ children, closeOnSelect, onSelect, postType, postTypes, taxonomies }) => {
    const [filters, setFilters] = useState({})
    const [modalOpen, setModalOpen] = useState(false)
    const [searchQuery, setSearchQuery] = useState('')
    const [selectedPostType, setSelectedPostType] = useState(postTypes ? postTypes[0] : '')

    const toggleModal = () => {
        setModalOpen(!modalOpen)
    }

    const handlePostSelect = post => {
        onSelect(post)
        if (closeOnSelect) {
            toggleModal()
        }
    }

    const handleFilterSelect = (slug, value) => {
        const restArg = getRestArgByTaxonomySlug(slug)

        setFilters({
            ...filters,
            [restArg]: value
        })
    }

    return (
        <>
            {children(toggleModal)}
            {
                modalOpen && (
                    <Modal className='admin-modal' onRequestClose={toggleModal}
                        title={_x('Select a post', 'admin', 'wp-surf-theme')}>
                        <div className='admin-modal__content'>
                            <div className='admin-modal__aside'>
                                <TextControl
                                    label={_x('Type to find posts', 'admin', 'wp-surf-theme')}
                                    onChange={value => setSearchQuery(value)}
                                    value={searchQuery}
                                />
                                <PostTypeFilter onSelect={value => setSelectedPostType(value)} postTypes={postTypes}
                                    value={selectedPostType}/>
                                {
                                    taxonomies.map(taxonomy => (
                                        <PostTaxonomyFilter key={taxonomy.slug}
                                            onSelect={value => handleFilterSelect(taxonomy.slug, value)}
                                            taxonomy={taxonomy}/>
                                    ))
                                }
                            </div>
                            <div className='admin-modal__main'>
                                <PostPickerList args={filters} onSelect={post => handlePostSelect(post)}
                                    postType={selectedPostType} searchQuery={searchQuery}/>
                            </div>
                        </div>
                    </Modal>
                )
            }
        </>
    )
}

PostPickerModal.propTypes = {
    closeOnSelect: PropTypes.bool,
    onSelect: PropTypes.func,
    postType: PropTypes.string,
    postTypes: PropTypes.array,
    taxonomies: PropTypes.array
}

PostPickerModal.defaultProps = {
    closeOnSelect: false,
    onSelect: () => {
    },
    postType: 'post',
    postTypes: ['post', 'page'],
    taxonomies: [
        {
            label: _x('Category', 'admin', 'wp-surf-theme'),
            slug: 'category'
        },
        {
            label: _x('Tags', 'admin', 'wp-surf-theme'),
            slug: 'post_tag'
        }
    ]
}

export default PostPickerModal
