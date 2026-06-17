import {
    _x,
    Fragment,
    RadioControl,
    withSelect
} from '@surf/js/gutenberg/packages'

const applyWithSelect = withSelect(select => ({ allPostTypes: select('core').getPostTypes({ per_page: -1 }) }))

const PostTypeFilter = ({ allPostTypes, onSelect, postTypes, value }) => {
    if (!allPostTypes) return <></>

    const options = allPostTypes
        .filter(type => (postTypes ? postTypes.includes(type.slug) : true))
        .map(type => ({
            value: type.slug,
            label: type.name
        }))

    return (
        <RadioControl
            label={_x('Type', 'admin', 'wp-surf-theme')}
            selected={value}
            options={options}
            onChange={option => onSelect(option)}
        />
    )
}

export default applyWithSelect(PostTypeFilter)
