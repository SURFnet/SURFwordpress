import { _x } from '@surf/js/gutenberg/packages'
import SurfPlaceholder from '@surf/js/gutenberg/component/Placeholder'

const Edit = () => {
    return (
        <SurfPlaceholder
            label={_x('Examples', 'admin', 'wp-surf-theme')}
            instructions={_x('This is a placeholder; settings should be configured through the Settings panel > Examples', 'admin', 'wp-surf-theme')}
        >
            <div className="surf-skeleton">
                <div className="surf-skeleton__item"/>
                <div className="surf-skeleton__item"/>
                <div className="surf-skeleton__item"/>
            </div>
        </SurfPlaceholder>
    )
}

export default Edit
