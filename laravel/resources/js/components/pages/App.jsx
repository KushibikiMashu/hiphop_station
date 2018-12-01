import React from 'react'
import ReactDOM from 'react-dom'
import Header from "../organisms/Header";
import Main from './Main'

export default function App(){
    return (
        <React.Fragment>
            <Header/>
            <Main/>
        </React.Fragment>
    )
}

if (document.getElementById('material')) {
    ReactDOM.render(<App />, document.getElementById('material'))
}

