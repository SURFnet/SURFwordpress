const { _x } = wp.i18n

export default class WidgetHelper {
    parent = null

    constructor () {
        jQuery('div.widgets-sortables').bind('sortstart', function (event, ui) {
            this.parent = ui.item.parent()[0]
        })

        jQuery('div.widgets-sortables').bind('sortstop', function (event, ui) {
            if (!ui.item[0].id.includes('-surf-widget')) {
                return
            }

            if (this.parent !== ui.item.parent()[0]) {
                ui.item[0].querySelector('.widget-inside form').innerText = _x('Please refresh the page to initialize the widget.', 'admin', 'wp-surf-theme')
            }
        })
    }

    static init () {
        new WidgetHelper()
    }
}
