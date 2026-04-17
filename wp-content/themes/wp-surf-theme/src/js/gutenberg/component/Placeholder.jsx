import { Placeholder } from '@surf/js/gutenberg/packages'

const SurfPlaceholder = ({ label, instructions, variant = 'list', children }) => {
    const skeleton = () => {
        if (children) {
            return (
                <div className="surf-settings">
                    {children}
                </div>
            )
        }

        switch (variant) {
        case 'cards':
            return (
                <div className="surf-skeleton surf-skeleton--cards">
                    <div/>
                    <div/>
                    <div/>
                </div>
            )

        case 'block':
            return (
                <div className="surf-skeleton surf-skeleton--block">
                    <div/>
                </div>
            )

        default:
            return (
                <div className="surf-skeleton surf-skeleton--list">
                    <div/>
                    <div/>
                    <div/>
                </div>
            )
        }
    }

    return (
        <Placeholder
            label={label}
            instructions={instructions}
            className="surf-placeholder"
        >
            {skeleton()}
        </Placeholder>
    )
}

export default SurfPlaceholder
