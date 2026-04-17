import PropTypes from 'prop-types'

import {
    _x,
    Modal,
    TextControl,
    useEffect,
    useState
} from '@surf/js/gutenberg/packages'

import Icon from '@surf/js/react/components/Icon'

const IconPicker = ({ children, icons, onSelect, sprite, value }) => {
    const [modalOpen, setModalOpen] = useState(false)
    const [searchQuery, setSearchQuery] = useState('')
    const [selection, setSelection] = useState(value || null)

    const toggleModal = () => {
        setModalOpen(!modalOpen)
    }

    useEffect(() => {
        onSelect(selection)
    }, [selection])

    return (
        <>
            {children(toggleModal)}
            {
                modalOpen && (
                    <Modal className='admin-modal' title={_x('Pick an icon', 'admin', 'wp-surf-theme')}
                        onRequestClose={toggleModal}>
                        <div className='admin-modal__content'>
                            <div className='admin-modal__aside'>
                                <TextControl
                                    label={_x('Type to find an icon', 'admin', 'wp-surf-theme')}
                                    onChange={value => setSearchQuery(value)}
                                    value={searchQuery}
                                />
                            </div>
                            <div className='admin-modal__main'>
                                <div className='icon-picker'>
                                    {
                                        icons.filter(icon => {
                                            if (searchQuery.trim() === '') return true
                                            return icon.indexOf(searchQuery) > -1
                                        }).map(icon => (
                                            <div className='icon-picker__option' key={icon}>
                                                <input className='icon-picker__option-input'
                                                    checked={icon === selection} id={icon} name='icon-picker'
                                                    onChange={() => setSelection(icon)} type='radio'
                                                    value={icon}/>
                                                <label className='icon-picker__option-label' htmlFor={icon}>
                                                    <Icon className='icon-picker__option-icon' icon={icon}
                                                        sprite={sprite}/>
                                                </label>
                                            </div>
                                        ))
                                    }
                                </div>
                            </div>
                        </div>
                    </Modal>
                )
            }
        </>
    )
}

IconPicker.propTypes = {
    icons: PropTypes.array,
    onSelect: PropTypes.func,
    sprite: PropTypes.string.isRequired,
    value: PropTypes.string
}

IconPicker.defaultProps = {
    icons: [],
    onSelect: () => {
    }
}

export default IconPicker
