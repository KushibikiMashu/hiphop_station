import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import CssBaseline from '@material-ui/core/CssBaseline';
import SearchAppBar from './SearchAppBar';
import NewSongs from './NewSongs'
import NewMCBattle from './NewMCBattle'
import ClippedDrawer from './ClippedDrawer'

import { BrowserRouter as Router, Route, Link } from 'react-router-dom';

export default class Material extends Component {
    render() {
        return (
            <React.Fragment>
                <CssBaseline />
                <ClippedDrawer />
            </React.Fragment>
        );
    }
}

if (document.getElementById('material')) {
    ReactDOM.render(<Material />, document.getElementById('material'));
}
