import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import CssBaseline from '@material-ui/core/CssBaseline';
import ClippedDrawer from './ClippedDrawer'

export default class Material extends Component {
    render() {
        return (
            <React.Fragment>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"></meta>
                <CssBaseline />
                <ClippedDrawer />
            </React.Fragment>
        );
    }
}

if (document.getElementById('material')) {
    ReactDOM.render(<Material />, document.getElementById('material'));
}
