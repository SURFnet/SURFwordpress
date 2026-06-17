import {
    CheckboxControl,
    Fragment,
    useEffect,
    useState,
    withSelect
} from '@surf/js/gutenberg/packages'

const applyWithSelect = withSelect((select, props) => {
    return {
        terms: select('core').getEntityRecords('taxonomy', props.taxonomy.slug, {
            per_page: -1
        })
    }
})

const PostTaxonomyFilter = ({ onSelect, taxonomy, terms }) => {
    const [selection, setSelection] = useState([])

    const options = terms?.map(term => ({
        value: term.id,
        label: term.name
    }))

    const handleChange = (checked, value) => {
        let newSelection

        if (checked) {
            newSelection = [...selection, value]
        } else {
            newSelection = [...selection].filter(item => item !== value)
        }

        setSelection(newSelection)
    }

    useEffect(() => {
        if (typeof onSelect === 'function') {
            onSelect(selection)
        }
    }, [selection])

    if (!terms || !options) return <></>

    return (
        <div>
            {options?.length !== 0 && (
                <>
                    <label>{taxonomy.label}</label>
                    {options.map(option => (
                        <CheckboxControl key={option.value}
                            label={option.label}
                            checked={selection.includes(option.value)}
                            onChange={checked => handleChange(checked, option.value)}
                        />
                    ))}
                </>
            )}
        </div>
    )
}

export default applyWithSelect(PostTaxonomyFilter)
