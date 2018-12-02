import React from 'react'
import ReactDOM from 'react-dom'
import Header from "../organisms/Header";
import Main from './Main'
import {BrowserRouter as Router} from 'react-router-dom'

export default function App() {
    return (
        <React.Fragment>
            <Header/>
            <Router basename='/'>
                <Main/>
            </Router>
        </React.Fragment>
    )
}

if (document.getElementById('root')) {
    ReactDOM.render(<App/>, document.getElementById('root'))
}
