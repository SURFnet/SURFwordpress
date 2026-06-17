import React, { StrictMode } from 'react'
import ReactDOM from 'react-dom'

class ComponentLoader {
    static init (components) {
        (new ComponentLoader()).run(components)
    }

    run (components) {
        Object.entries(components).forEach(([name, Component]) => {
            this.load(name, Component)
        })
    }

    load (name, Component) {
        Array.from(
            document.querySelectorAll(`[data-component="${name}"]`)
        ).forEach(element => {
            const props = element.dataset.props ? JSON.parse(element.dataset.props) : {}
            element.removeAttribute('data-props')

            ReactDOM.render(
                <StrictMode>
                    <Component {...props}/>
                </StrictMode>,
                element
            )
        })
    }
}

export default ComponentLoader
